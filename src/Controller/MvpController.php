<?php

namespace App\Controller;

use App\Domain\SheetTemplate;
use App\Entity\Campaign;
use App\Entity\CharacterSheet;
use App\Entity\User;
use App\Entity\Wiki;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/mvp')]
class MvpController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}

    private function input(Request $request, array $allowed): array
    {
        $data = $request->toArray();
        if (array_diff(array_keys($data), $allowed)) {
            throw new BadRequestHttpException('Champ non autorisé.');
        }
        return $data;
    }

    private function text(array $data, string $key, int $max = 100000, bool $required = false): string
    {
        $value = $data[$key] ?? '';
        if (!is_string($value) || mb_strlen($value) > $max || ($required && trim($value) === '')) {
            throw new BadRequestHttpException('Champ invalide : '.$key.'.');
        }
        return $value;
    }

    private function rpg(int $id): Wiki
    {
        $wiki = $this->em->getRepository(Wiki::class)->findOneBy(['id' => $id, 'user' => $this->getUser()]);
        if (!$wiki) { throw $this->createNotFoundException(); }
        return $wiki;
    }

    private function campaign(int $id): Campaign
    {
        $campaign = $this->em->getRepository(Campaign::class)->findOneBy(['id' => $id, 'owner' => $this->getUser()]);
        if (!$campaign) { throw $this->createNotFoundException(); }
        return $campaign;
    }

    private function sheet(int $id): CharacterSheet
    {
        $sheet = $this->em->getRepository(CharacterSheet::class)->find($id);
        if (!$sheet || $sheet->getCampaign()->getOwner()->getId() !== $this->getUser()->getId()) {
            throw $this->createNotFoundException();
        }
        return $sheet;
    }

    private function rpgData(Wiki $wiki): array
    {
        $content = $wiki->getContent() ?? '';
        $delta = json_decode($content, true);
        if (is_array($delta) && isset($delta['ops']) && is_array($delta['ops'])) {
            $content = implode('', array_map(fn ($op) => is_string($op['insert'] ?? null) ? $op['insert'] : '', $delta['ops']));
        }
        return ['id' => $wiki->getId(), 'name' => $wiki->getName(), 'description' => $content,
            'fields' => $wiki->getCharacterTemplate(), 'templateVersion' => $wiki->getTemplateVersion()];
    }

    private function sheetData(CharacterSheet $sheet): array
    {
        return ['id' => $sheet->getId(), 'campaignId' => $sheet->getCampaign()->getId(), 'name' => $sheet->getName(),
            'notes' => $sheet->getNotes(), 'fields' => $sheet->getTemplate(), 'values' => (object) $sheet->getValues(),
            'templateVersion' => $sheet->getTemplateVersion()];
    }

    private function campaignData(Campaign $campaign, bool $withSheets = false): array
    {
        $data = ['id' => $campaign->getId(), 'rpgId' => $campaign->getWiki()->getId(), 'rpgName' => $campaign->getWiki()->getName(),
            'name' => $campaign->getName(), 'description' => $campaign->getDescription(), 'notes' => $campaign->getNotes(),
            'journal' => $campaign->getJournal()];
        if ($withSheets) {
            $data['sheets'] = array_map($this->sheetData(...), $this->em->getRepository(CharacterSheet::class)->findBy(['campaign' => $campaign], ['id' => 'ASC']));
        }
        return $data;
    }

    #[Route('/rpgs', methods: ['GET'])]
    public function listRpgs(): JsonResponse
    {
        return $this->json(array_map($this->rpgData(...), $this->em->getRepository(Wiki::class)->findBy(['user' => $this->getUser()], ['id' => 'DESC'])));
    }

    #[Route('/rpgs', methods: ['POST'])]
    public function createRpg(Request $request): JsonResponse
    {
        $data = $this->input($request, ['name', 'description', 'fields']);
        $name = $this->text($data, 'name', 255, true);
        $description = $this->text($data, 'description');
        $fields = SheetTemplate::fields($data['fields'] ?? []);
        $wiki = new Wiki();
        $wiki->setName($name);
        $wiki->setContent(json_encode(['ops' => [['insert' => $description."\n"]]], JSON_THROW_ON_ERROR));
        $wiki->setCharacterTemplate($fields);
        $wiki->setUser($this->getUser());
        $this->em->persist($wiki);
        $this->em->flush();
        return $this->json($this->rpgData($wiki), 201);
    }

    #[Route('/rpgs/{id}', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function updateRpg(int $id, Request $request): JsonResponse
    {
        $wiki = $this->rpg($id);
        $data = $this->input($request, ['name', 'description', 'fields', 'expectedTemplateVersion']);
        if (($data['expectedTemplateVersion'] ?? null) !== $wiki->getTemplateVersion()) {
            throw new ConflictHttpException('Le modèle a changé. Rechargez le JDR avant de modifier.');
        }
        $name = $this->text($data, 'name', 255, true);
        $description = $this->text($data, 'description');
        $fields = SheetTemplate::fields($data['fields'] ?? []);
        $wiki->setName($name);
        $wiki->setContent(json_encode(['ops' => [['insert' => $description."\n"]]], JSON_THROW_ON_ERROR));
        $wiki->setCharacterTemplate($fields);
        try { $this->em->flush(); }
        catch (\Doctrine\ORM\OptimisticLockException $e) { throw new ConflictHttpException('Le modèle a changé. Rechargez le JDR.', $e); }
        return $this->json($this->rpgData($wiki));
    }

    #[Route('/campaigns', methods: ['GET'])]
    public function listCampaigns(): JsonResponse
    {
        return $this->json(array_map(fn ($campaign) => $this->campaignData($campaign),
            $this->em->getRepository(Campaign::class)->findBy(['owner' => $this->getUser()], ['id' => 'DESC'])));
    }

    #[Route('/campaigns', methods: ['POST'])]
    public function createCampaign(Request $request): JsonResponse
    {
        $data = $this->input($request, ['rpgId', 'name', 'description']);
        if (!is_int($data['rpgId'] ?? null)) { throw new BadRequestHttpException('JDR requis.'); }
        $wiki = $this->rpg($data['rpgId']);
        $name = $this->text($data, 'name', 255, true);
        $description = $this->text($data, 'description');
        $campaign = new Campaign();
        $campaign->setOwner($this->getUser());
        $campaign->setWiki($wiki);
        $campaign->setName($name);
        $campaign->setDescription($description);
        $this->em->persist($campaign);
        $this->em->flush();
        return $this->json($this->campaignData($campaign, true), 201);
    }

    #[Route('/campaigns/{id}', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getCampaign(int $id): JsonResponse
    {
        return $this->json($this->campaignData($this->campaign($id), true));
    }

    #[Route('/campaigns/{id}', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function updateCampaign(int $id, Request $request): JsonResponse
    {
        $campaign = $this->campaign($id);
        $data = $this->input($request, ['name', 'description', 'notes']);
        $name = $this->text($data, 'name', 255, true);
        $description = $this->text($data, 'description');
        $notes = $this->text($data, 'notes');
        $campaign->setName($name);
        $campaign->setDescription($description);
        $campaign->setNotes($notes);
        $this->em->flush();
        return $this->json($this->campaignData($campaign, true));
    }

    #[Route('/campaigns/{id}/sessions', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function addSession(int $id, Request $request): JsonResponse
    {
        $campaign = $this->campaign($id);
        $data = $this->input($request, ['title', 'date', 'notes']);
        $title = $this->text($data, 'title', 255, true);
        $date = $this->text($data, 'date', 10, true);
        $notes = $this->text($data, 'notes');
        $parsed = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);
        if (!$parsed || $parsed->format('Y-m-d') !== $date) { throw new BadRequestHttpException('Date invalide.'); }
        $journal = $campaign->getJournal();
        if (count($journal) >= 1000) { throw new BadRequestHttpException('Limite de séances atteinte.'); }
        $journal[] = ['id' => bin2hex(random_bytes(8)), 'title' => $title, 'date' => $date, 'notes' => $notes];
        $campaign->setJournal($journal);
        $this->em->flush();
        return $this->json($this->campaignData($campaign, true), 201);
    }

    #[Route('/campaigns/{id}/sheets', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function createSheet(int $id, Request $request): JsonResponse
    {
        $campaign = $this->campaign($id);
        $data = $this->input($request, ['name', 'notes', 'values']);
        $fields = $campaign->getWiki()->getCharacterTemplate();
        $name = $this->text($data, 'name', 255, true);
        $notes = $this->text($data, 'notes');
        $values = SheetTemplate::values($data['values'] ?? [], $fields);
        $sheet = new CharacterSheet();
        $sheet->setCampaign($campaign);
        $sheet->setName($name);
        $sheet->setNotes($notes);
        $sheet->setTemplate($fields);
        $sheet->setTemplateVersion($campaign->getWiki()->getTemplateVersion());
        $sheet->setValues($values);
        $this->em->persist($sheet);
        $this->em->flush();
        return $this->json($this->sheetData($sheet), 201);
    }

    #[Route('/sheets/{id}', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function updateSheet(int $id, Request $request): JsonResponse
    {
        $sheet = $this->sheet($id);
        $data = $this->input($request, ['name', 'notes', 'values']);
        $name = $this->text($data, 'name', 255, true);
        $notes = $this->text($data, 'notes');
        $values = SheetTemplate::values($data['values'] ?? [], $sheet->getTemplate());
        $sheet->setName($name);
        $sheet->setNotes($notes);
        $sheet->setValues($values);
        $this->em->flush();
        return $this->json($this->sheetData($sheet));
    }
}
