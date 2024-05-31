<?php

namespace App\Controller;

use App\Repository\WikiRepository;
use App\Entity\Wiki;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class WikiController extends AbstractController
{
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
        $wikis = $repository->findAll();
        return $this->json($wikis);
    }

    #[Route('/api/wikis/{wiki}', name: "wiki.getOne", methods: ["GET"])]
    public function getOne(Wiki $wiki, SerializerInterface $serializer, Request $request, WikiRepository $repository): Response
    {

        $wiki = $repository->findOneById($wiki->getId());
        return new Response(
            $serializer->serialize($wiki, 'json'),
            Response::HTTP_OK,
            ['Content-type' => 'application/json']
         );
    }

    #[Route('/api/wikis', name: 'wiki.create', methods: ["POST"])]
    public function createWiki(Request $request, WikiRepository $repository, SerializerInterface $serializer): Response
    {
        $wiki = $serializer->deserialize($request->getContent(), Wiki::class, 'json');
        
        $repository->addWiki($wiki);
        $idWiki = $wiki->getId();
        //$location = $this->generateUrl('wiki.get', ['wiki' => $wiki->getId()], UrlGeneratorInterface::ABSOLUTE_URL); ['Location' => $location]
        $response = new Response(
            $serializer->serialize($repository->findOneById($idWiki) , 'json'),
            Response::HTTP_CREATED,
            ['Content-type' => 'application/json']
         );
         
         return $response;
    }


    

    #[Route('/api/wikis/{wiki}', name: "wiki.delete", methods: ["DELETE"])]
    public function deleteWiki(Wiki $wiki, WikiRepository $repository): JsonResponse
    {
        $repository->removeWiki($wiki);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/wikis/{wiki}', name:'wiki.update', methods: ["PUT"])]
    public function updateWiki(Wiki $wiki, SerializerInterface $serializer, Request $request, WikiRepository $repository): JsonResponse
    {
        $updateWiki = $serializer->deserialize($request->getContent(), Wiki::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $wiki]);
        $repository->updateWiki($updateWiki);
        
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

}
