<?php  

namespace App\Controller;

use App\Repository\BestiaryRepository;


use App\Entity\Wiki;
use App\Entity\Bestiary;
use App\Form\RegistrationFormType;

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

class BestiaryController extends AbstractController
{

    #[Route('/api/wikis/{wiki}/bestiaries', name: 'add.bestiary', methods: ['POST'])]
    #[IsGranted(WikiElementVoter::EDIT, subject: 'bestiary')]
    public function addbestiary(Bestiary $bestiary, Wiki $wiki, Request $request, BestiaryRepository $bestiaryRepository, WikiRepository $wikiRepository,SerializerInterface $serializer): Response
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
        $bestiaryRepository->removeBestiary($bestiary, $wiki);
        $wiki = $wikiRepository->findOneById($wiki->getId());
        return $this->json($wiki, 200, [], [
            'groups' => ['wiki.index','wiki.details']
        ]);
    }

    #[Route('/api/wikis/{wiki}/bestiaries/{bestiary}', name: 'update.bestiary', methods: ['PUT'])]
    #[IsGranted(WikiElementVoter::EDIT, subject: 'bestiary')]
    public function updateBestiary(Bestiary $bestiary, Wiki $wiki, Request $request, BestiaryRepository $bestiaryRepository, WikiRepository $wikiRepository, SerializerInterface $serializer): Response
    {
        $bestiary = $serializer->deserialize($request->getContent(), Bestiary::class, 'json', ['object_to_populate' => $bestiary]);
        $bestiaryRepository->updateBestiary($bestiary, $wiki);

        $wiki = $wikiRepository->findOneById($wiki->getId());
        return $this->json($wiki, 200, [], [
            'groups' => ['wiki.index','wiki.details']
        ]);
    }
}