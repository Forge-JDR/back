<?php  

namespace App\Controller;



use App\Entity\Wiki;
use App\Entity\Race;

use App\Repository\RaceRepository;
use App\Repository\WikiRepository;
use App\Security\Voter\WikiElementVoter;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class RaceController extends AbstractController
{

    #[Route('/api/wikis/{wiki}/races', name: 'add.race', methods: ['POST'])]
    #[IsGranted(WikiElementVoter::EDIT, subject: 'race')]
    public function addRace(Race $race, Wiki $wiki, Request $request, RaceRepository $raceRepository, WikiRepository $wikiRepository,SerializerInterface $serializer): Response
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
        $raceRepository->removeRace($race, $wiki);
        $wiki = $wikiRepository->findOneById($wiki->getId());
        return $this->json($wiki, 200, [], [
            'groups' => ['wiki.index','wiki.details']
        ]);
    }

}