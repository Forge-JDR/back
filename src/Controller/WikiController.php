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
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/WikiController.php',
        ]);
    }


    #[Route('/api/wikis', name: "wiki.create", methods: ["POST"])]
    public function createWiki(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        $wiki = $serializer->deserialize($request->getContent(), Wiki::class, 'json');
        $wiki->setCreatedAt(new \DateTimeImmutable());
        $wiki->setStatus('active');
        $entityManager->persist($wiki);
        $entityManager->flush();

        $jsonWiki = $serializer->serialize($wiki, 'json');
        $location = $this->generateUrl('wiki.get', ['wiki' => $wiki->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonWiki, Response::HTTP_CREATED, ['Location' => $location], true);
    }


    #[Route('/api/wikis', name: "wiki.getAll")]
    public function getAll(WikiRepository $repository): JsonResponse
    {
        $wikis = $repository->findAll();
        return $this->json($wikis);
    }

    #[Route('/api/wikis/{wiki}', name: "wiki.delete", methods: ["DELETE"])]
    public function deleteWiki(Wiki $wiki, WikiRepository $repository): JsonResponse
    {
        $repository->deleteWiki($wiki);
        $repository->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route("/api/wikis/{wiki}", name:"wiki.update", methods: ["PUT, PATCH"])]
    public function updateWiki(Wiki $wiki, WikiRepository $repository, SerializerInterface $serializer, Request $request, EntityManagerInterface $entityManager): JsonResponse

    {
        $updateWiki = $serializer->deserialize($request->getContent(), Wiki::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $wiki]);
        $entityManager->persist($updateWiki);
        $entityManager->flush();
        
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

}
