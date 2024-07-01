<?php

namespace App\Controller;

use App\Repository\ScenariRepository;
use App\Repository\WikiRepository;
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

    #[Route('/api/scenari', name: "scenari.getAll", methods: ["GET"])]
    public function getAll(ScenariRepository $repository): JsonResponse
    {
        $scenari = $repository->findAll();
        return $this->json($scenari, 200, [], [
            'groups' => 'scenari.index'
        ]);
    }

    #[Route('/api/scenari/{scenari}', name: "scenari.getOne", methods: ["GET"])]
    public function getOne(Scenari $scenari, SerializerInterface $serializer, Request $request, ScenariRepository $repository): Response
    {

        $scenari = $repository->findOneById($scenari->getId());
        return $this->json($scenari, 200, [], [
            'groups' => ['scenari.index','scenari.details']
        ]);
    }

    #[Route('/api/scenari', name: "scenari.create", methods: ["POST"])]
    public function createScenari(Request $request, ScenariRepository $repository, WikiRepository $repositoryWiki, SerializerInterface $serializer): JsonResponse
    {
        $scenari = $serializer->deserialize($request->getContent(), Scenari::class, 'json');

        $repository->addScenari($scenari);
        $idScenari = $scenari->getId();
        $wiki = $repositoryWiki->findOneById($scenari->wiki_id);

        // $jsonScenari = $serializer->serialize($scenari, 'json');
        $response = new Response(
            $serializer->serialize($repository->findOneById($idScenari) , 'json'),
            Response::HTTP_CREATED,
            ['Content-type' => 'application/json']
         );
         
         return $response;
    }
}
