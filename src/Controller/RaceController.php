<?php  

namespace App\Controller;



use App\Entity\Wiki;
use App\Entity\Race;

use App\Repository\RaceRepository;
use App\Repository\WikiRepository;
use App\Security\Voter\WikiElementVoter;
use App\Security\Voter\WikiVoter;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class RaceController extends AbstractController
{

    #[Route('/api/wikis/{wiki}/races', name: 'add.race', methods: ['POST'])]
    #[IsGranted(WikiVoter::EDIT, subject: 'wiki')]
    public function addRace(Wiki $wiki, Request $request, RaceRepository $raceRepository, WikiRepository $wikiRepository,SerializerInterface $serializer): Response
    {
        $race = $serializer->deserialize($request->getContent(), Race::class, 'json');
        $race->setWiki($wiki);
        $raceRepository->addRace($race, $wiki);

        $wiki = $wikiRepository->findOneById($wiki->getId());
        return $this->json($wiki, 200, [], [
            'groups' => ['wiki.index','wiki.details']
        ]);
    }

    #[Route('/api/wikis/{wiki}/races/{race}', name: 'delete.race', methods: ['DELETE'])]
    #[IsGranted(WikiElementVoter::EDIT, subject: 'race')]
    public function deleterace(Race $race, Wiki $wiki, RaceRepository $raceRepository, WikiRepository $wikiRepository): Response
    {
        // Vérifiez si le job appartient bien au wiki
        if ($race->getWiki()->getId() !== $wiki->getId()) {
            return $this->json(['error' => 'Race does not belong to the specified wiki.'], 400);
        }
        $raceRepository->removeRace($race, $wiki);
        $wiki = $wikiRepository->findOneById($wiki->getId());
        return $this->json($wiki, 200, [], [
            'groups' => ['wiki.index','wiki.details']
        ]);
    }

    #[Route('/api/wikis/{wiki}/races/{race}', name: 'update.race', methods: ['PUT'])]
    #[IsGranted(WikiElementVoter::EDIT, subject: 'race')]
    public function updateRace(Race $race, Wiki $wiki, Request $request, RaceRepository $raceRepository, WikiRepository $wikiRepository, SerializerInterface $serializer): Response
    {
        // Récupérer l'entité Wiki
        $wiki = $wikiRepository->find($wiki);
        if (!$wiki) {
            return $this->json(['error' => 'Wiki not found.'], 404);
        }
    
        // Récupérer l'entité Job
        $job = $raceRepository->find($race);
        if (!$job) {
            return $this->json(['error' => 'Race not found.'], 404);
        }
    
        // Vérifiez si le job appartient bien au wiki
        if ($job->getWiki()->getId() !== $wiki->getId()) {
            return $this->json(['error' => 'Race does not belong to the specified wiki.'], 400);
        }
        $race = $serializer->deserialize($request->getContent(), Race::class, 'json', ['object_to_populate' => $race]);
        
        $raceRepository->updateRace($race, $wiki);

        $wiki = $wikiRepository->findOneById($wiki->getId());
        return $this->json($wiki, 200, [], [
            'groups' => ['wiki.index','wiki.details']
        ]);
    }

}