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


class WikiAdminController extends AbstractController
{

    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }


    #[Route('/api/admin/wikis', name: "wiki.getAllAdmin", methods: ["GET"])]
    public function getAll(WikiRepository $repository): JsonResponse
    {
        $wikis = $repository->findAll();
        return $this->json($wikis, 200, [], [
            'groups' => ['wiki.index','picture.details']
        ]);
    }

    #[Route('/api/admin/wikis/{wiki}', name: "wiki.getOneAdmin", methods: ["GET"])]
    public function getOne(Wiki $wiki, SerializerInterface $serializer, Request $request, WikiRepository $repository): JsonResponse
    {

        $wiki = $repository->findOneById($wiki->getId());
        return $this->json($wiki, 200, [], [
            'groups' => ['wiki.index','wiki.details']
        ]);
    }

    

    #[Route('/api/admin/wikis/{wiki}', name: "wiki.deleteAdmin", methods: ["DELETE"])]
    public function deleteWiki(Wiki $wiki, WikiRepository $repository): JsonResponse
    {
        $repository->removeWiki($wiki);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/admin/wikis/{wiki}', name:'wiki.updateAdmin', methods: ["PUT"])]
    public function updateWiki(Wiki $wiki, SerializerInterface $serializer, Request $request, WikiRepository $repository): JsonResponse
    {
        $updateWiki = $serializer->deserialize($request->getContent(), Wiki::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $wiki]);
        $repository->updateWiki($updateWiki);
        
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }


}
