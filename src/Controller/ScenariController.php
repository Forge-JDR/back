<?php

namespace App\Controller;

use App\Repository\ScenariRepository;
use App\Entity\Scenari;
use App\Entity\Wiki;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

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
            'groups' => ['scenari.index','scenari.details',]
        ]);
    }

    #[Route('/api/scenari/{wiki}', name: "scenari.create", methods: ["POST"])]
    public function createScenari(Wiki $wiki, Request $request, ScenariRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $scenari = $serializer->deserialize($request->getContent(), Scenari::class, 'json');
        $scenari->setWiki($wiki);
        $repository->addScenari($scenari, $wiki);

        $idScenari = $repository->findOneById($scenari->getId());
        return $this->json($idScenari, 200, [], [
            'groups' => ['scenari.index','scenari.details']
        ]);
    }

    #[Route('/api/scenari/{scenari}', name:'scenari.update', methods: ["PUT"])]
    public function updateScenari(Scenari $scenari, SerializerInterface $serializer, Request $request, ScenariRepository $repository): JsonResponse
    {
        $updateScenari = $serializer->deserialize($request->getContent(), Scenari::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $scenari]);
        $repository->updateScenari($updateScenari);
        
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/scenari/{scenari}', name: "scenari.delete", methods: ["DELETE"])]
    public function deleteScenari(Scenari $scenari, ScenariRepository $repository): JsonResponse
    {
        $repository->deleteScenari($scenari);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
