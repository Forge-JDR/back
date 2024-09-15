<?php  

namespace App\Controller;

use App\Repository\UserRepository;


use App\Entity\Wiki;
use App\Entity\Scenario;
use App\Security\Voter\WikiVoter;
use App\Form\RegistrationFormType;
use App\Repository\ScenarioRepository;
use App\Repository\WikiRepository;
use App\Security\Voter\WikiElementVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class ScenarioController extends AbstractController
{

    #[Route('/api/wikis/{wiki}/scenarios', name: 'add.Scenario', methods: ['POST'])]
    #[IsGranted(WikiVoter::EDIT, subject: 'wiki')]
    public function addScenario(Wiki $wiki, Request $request, ScenarioRepository $ScenarioRepository, WikiRepository $wikiRepository,SerializerInterface $serializer): Response
    {
        $Scenario = $serializer->deserialize($request->getContent(), Scenario::class, 'json');
        $Scenario->setWiki($wiki);
        $ScenarioRepository->addScenario($Scenario, $wiki);

        $wiki = $wikiRepository->findOneById($wiki->getId());
        return $this->json($wiki, 200, [], [
            'groups' => ['wiki.index','wiki.details']
        ]);
    }

    #[Route('/api/wikis/{wiki}/scenarios/{Scenario}', name: 'delete.Scenario', methods: ['DELETE'])]
    #[IsGranted(WikiElementVoter::EDIT, subject: 'Scenario')]
    public function deleteScenario(Scenario $Scenario, Wiki $wiki, ScenarioRepository $ScenarioRepository, WikiRepository $wikiRepository): Response
    {
        $ScenarioRepository->removeScenario($Scenario, $wiki);
        $wiki = $wikiRepository->findOneById($wiki->getId());
        return $this->json($wiki, 200, [], [
            'groups' => ['wiki.index','wiki.details']
        ]);
    }

    #[Route('/api/wikis/{wiki}/scenarios/{Scenario}', name: 'update.Scenario', methods: ['PUT'])]
    #[IsGranted(WikiElementVoter::EDIT, subject: 'Scenario')]
    public function updateScenario(Scenario $Scenario, Wiki $wiki, Request $request, ScenarioRepository $ScenarioRepository, WikiRepository $wikiRepository, SerializerInterface $serializer): Response
    {
        $Scenario = $serializer->deserialize($request->getContent(), Scenario::class, 'json', ['object_to_populate' => $Scenario]);
        $ScenarioRepository->updateScenario($Scenario, $wiki);

        $wiki = $wikiRepository->findOneById($wiki->getId());
        return $this->json($wiki, 200, [], [
            'groups' => ['wiki.index','wiki.details']
        ]);
    }

}