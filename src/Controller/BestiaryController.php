<?php  

namespace App\Controller;

use App\Repository\BestiaryRepository;


use App\Entity\Wiki;
use App\Entity\Bestiary;
use App\Form\RegistrationFormType;

use App\Repository\WikiRepository;
use App\Security\Voter\WikiElementVoter;
use App\Security\Voter\WikiVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BestiaryController extends AbstractController
{

    #[Route('/api/wikis/{wiki}/bestiaries', name: 'add.bestiary', methods: ['POST'])]
    #[IsGranted(WikiVoter::EDIT, subject: 'wiki')]
    public function addbestiary(Wiki $wiki, Request $request, BestiaryRepository $bestiaryRepository, WikiRepository $wikiRepository,SerializerInterface $serializer): Response
    {
        $bestiary = $serializer->deserialize($request->getContent(), bestiary::class, 'json');
        $bestiary->setWiki($wiki);
        $bestiaryRepository->addBestiary($bestiary, $wiki);

        $wiki = $wikiRepository->findOneById($wiki->getId());
        return $this->json($wiki, 200, [], [
            'groups' => ['wiki.index','wiki.details']
        ]);
    }

    #[Route('/api/wikis/{wiki}/bestiaries/{bestiary}', name: 'delete.bestiary', methods: ['DELETE'])]
    #[IsGranted(WikiElementVoter::EDIT, subject: 'bestiary')]
    public function deletebestiary(Bestiary $bestiary, Wiki $wiki, bestiaryRepository $bestiaryRepository, WikiRepository $wikiRepository): Response
    {
        // Vérifiez si le bestiary appartient bien au wiki
        if ($bestiary->getWiki()->getId() !== $wiki->getId()) {
            return $this->json(['error' => 'Bestiary does not belong to the specified wiki.'], 400);
        }

        $bestiaryRepository->removeBestiary($bestiary, $wiki);
        $wiki = $wikiRepository->findOneById($wiki->getId());
        return $this->json($wiki, 200, [], [
            'groups' => ['wiki.index','wiki.details']
        ]);
    }

    #[Route('/api/wikis/{wiki}/bestiaries/{bestiary}', name: 'update.bestiary', methods: ['PUT'])]
    #[IsGranted(WikiVoter::EDIT, subject: 'wiki')]
    public function updateBestiary(Wiki $wiki, Bestiary $bestiary, Request $request, BestiaryRepository $bestiaryRepository, WikiRepository $wikiRepository, SerializerInterface $serializer): Response
    {
        // Récupérer l'entité Wiki
        $wiki = $wikiRepository->find($wiki);
        if (!$wiki) {
            return $this->json(['error' => 'Wiki not found.'], 404);
        }
    
        // Récupérer l'entité Job
        $job = $bestiaryRepository->find($bestiary);
        if (!$job) {
            return $this->json(['error' => 'Bestiary not found.'], 404);
        }
    
        // Vérifiez si le job appartient bien au wiki
        if ($job->getWiki()->getId() !== $wiki->getId()) {
            return $this->json(['error' => 'Bestiary does not belong to the specified wiki.'], 400);
        }

        $bestiary = $serializer->deserialize($request->getContent(), Bestiary::class, 'json', ['object_to_populate' => $bestiary]);
        $bestiaryRepository->updateBestiary($bestiary, $wiki);

        $wiki = $wikiRepository->findOneById($wiki->getId());
        return $this->json($wiki, 200, [], [
            'groups' => ['wiki.index','wiki.details']
        ]);
    }
}