<?php

namespace App\Controller;

use App\Repository\WikiRepository;
use App\Entity\Wiki;
use App\Repository\PictureRepository;
use App\Security\Voter\WikiVoter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Flex\Recipe;

class WikiController extends AbstractController
{

    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }


    #[Route('/wiki', name: 'app_wiki')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to wiki controller!',
            'path' => 'src/Controller/WikiController.php',
        ]);
    }

    #[Route('/api/wikis', name: "wiki.getAll", methods: ["GET"])]
    public function getAll(WikiRepository $repository): JsonResponse
    {
        $wikis = $repository->findAllWithStatus("published");
        return $this->json($wikis, 200, [], [
            'groups' => ['wiki.index','picture.details']
        ]);
    }

    #[Route('/api/wikis/{wiki}', name: "wiki.getOne", methods: ["GET"])]
    #[IsGranted(WikiVoter::VIEW, subject: 'wiki')]
    public function getOne(Wiki $wiki, WikiRepository $repository): JsonResponse
    {
        $wiki = $repository->findOneById($wiki->getId());
        return $this->json($wiki, 200, [], [
            'groups' => ['wiki.index','wiki.details','picture.details']
        ]);
    }

    #[Route('/api/wikis', name: 'wiki.create', methods: ["POST"])]

    public function createWiki(Request $request, WikiRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $wiki = $serializer->deserialize($request->getContent(), Wiki::class, 'json');
        $wiki->setUser($this->getUser());
        $repository->addWiki($wiki);

        $wiki = $repository->findOneById($wiki->getId());
        return $this->json($wiki, 200, [], [
            'groups' => ['wiki.index','wiki.details']
        ]);
    }

    #[Route('/api/wikis/{wiki}/pictures', name: "wiki.add.picture", methods: ["POST"])]
    #[IsGranted(WikiVoter::EDIT, subject: 'wiki')]
    public function addPicture(Wiki $wiki, WikiRepository $wikiRepository, PictureRepository $pictureRepository,Request $request): JsonResponse
    {
        $picture = $request->files->get('file');
        $fileName = $request->files->get('file')->getClientOriginalName();

        // Delete physical picture with real path if it already exists
        if($wiki->getImageFile() !== null){
            $pictureRepository->removePictureFromFileSystem($wiki->getImageFile());
        }

        $wikiRepository->setPictureFile($wiki,$picture, $fileName);
        $wiki = $wikiRepository->findOneById($wiki->getId());
        return $this->json($wiki, 200, [], [
            'groups' => ['wiki.index','wiki.details','picture.details']
        ]);
    }

    

    #[Route('/api/wikis/{wiki}', name: "wiki.delete", methods: ["DELETE"])]
    #[IsGranted(WikiVoter::DELETE, subject: 'wiki')]
    public function deleteWiki(Wiki $wiki, WikiRepository $repository): JsonResponse
    {
        $repository->removeWiki($wiki);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/wikis/{wiki}', name:'wiki.update', methods: ["PUT"])]
    #[IsGranted(WikiVoter::EDIT, subject: 'wiki')]
    public function updateWiki(Wiki $wiki, SerializerInterface $serializer, Request $request, WikiRepository $repository): JsonResponse
    {
        $updateWiki = $serializer->deserialize($request->getContent(), Wiki::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $wiki]);
        $repository->updateWiki($updateWiki);
        
        $wiki = $repository->findOneById($wiki->getId());
        return $this->json($wiki, 200, [], [
            'groups' => ['wiki.index','wiki.details']
        ]);
    }

}
