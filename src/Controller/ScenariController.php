<?php

namespace App\Controller;

use App\Repository\ScenariRepository;
use App\Entity\Scenari;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ScenariController extends AbstractController
{
    #[Route('/scenari', name: 'app_scenari')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ScenariController.php',
        ]);
    }

    #[Route('/api/scenari', name: "scnerai.getAll", methods: ["GET"])]
    public function getAll(ScenariRepository $repository): JsonResponse
    {
        $scenari = $repository->findAll();
        return $this->json($scenari);
    }

    #[Route('/api/scenari', name: "scenari.create", methods: ["POST"])]
    public function createScenari(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        $scenari = $serializer->deserialize($request->getContent(), Scenari::class, 'json');
        $entityManager->persist($scenari);
        $entityManager->flush();

        $jsonScenari = $serializer->serialize($scenari, 'json');
        $location = $this->generateUrl('scenari.get', ['scenari' => $scenari->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonScenari, Response::HTTP_CREATED, ['Location' => $location], true);
    }
}
