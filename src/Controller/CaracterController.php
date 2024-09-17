<?php

namespace App\Controller;

use App\Repository\CaracterRepository;
use App\Entity\Caracter;
use App\Repository\PictureRepository;
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
use App\Security\Voter\CaracterVoter;

class CaracterController extends AbstractController
{

    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }


    #[Route('/caracter', name: 'app_caracter')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to caracter controller!',
            'path' => 'src/Controller/CaracterController.php',
        ]);
    }


    #[Route('/api/caracters/{caracter}', name: "caracter.getOne", methods: ["GET"])]
    #[IsGranted(CaracterVoter::VIEW, subject: 'caracter')]
    public function getOne(Caracter $caracter, CaracterRepository $repository): JsonResponse
    {
        $caracter = $repository->findOneById($caracter->getId());
        return $this->json($caracter, 200, [], [
            'groups' => ['caracter.index','caracter.details','picture.details']
        ]);
    }

    #[Route('/api/caracters', name: 'caracter.create', methods: ["POST"])]

    public function createCaracter(Request $request, CaracterRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $caracter = $serializer->deserialize($request->getContent(), Caracter::class, 'json');
        $caracter->setUser($this->getUser());
        $repository->addCaracter($caracter);

        $caracter = $repository->findOneById($caracter->getId());
        return $this->json($caracter, 200, [], [
            'groups' => ['caracter.index','caracter.details']
        ]);
    }

    #[Route('/api/caracters/{caracter}/pictures', name: 'caracter.add.picture', methods: ['POST'])]
    #[IsGranted('EDIT', subject: 'caracter')]
    public function addPicture(Caracter $caracter, CaracterRepository $caracterRepository, PictureRepository $pictureRepository, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $picture = $request->files->get('file');
        
        if (!$picture) {
            return $this->json(['error' => 'No file uploaded'], 400);
        }

        $fileName = $picture->getClientOriginalName();

        // Delete physical picture with real path if it already exists
        if ($caracter->getImageFile() !== null) {
            $pictureRepository->removePictureFromFileSystem($caracter->getImageFile());
        }

        $caracterRepository->setPictureFile($caracter, $picture, $fileName);
        $entityManager->flush();
        
        $caracter = $caracterRepository->findOneById($caracter->getId());

        return $this->json($caracter, 200, [], [
            'groups' => ['caracter.index', 'caracter.details', 'picture.details']
        ]);
    }

    

    #[Route('/api/caracters/{caracter}', name: "caracter.delete", methods: ["DELETE"])]
    #[IsGranted(CaracterVoter::DELETE, subject: 'caracter')]
    public function deleteCaracter(Caracter $caracter, CaracterRepository $repository): JsonResponse
    {
        $repository->removeCaracter($caracter);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/caracters/{caracter}', name:'caracter.update', methods: ["PUT"])]
    #[IsGranted(CaracterVoter::EDIT, subject: 'caracter')]
    public function updateCaracter(Caracter $caracter, SerializerInterface $serializer, Request $request, CaracterRepository $repository): JsonResponse
    {
        $updateCaracter = $serializer->deserialize($request->getContent(), Caracter::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $caracter]);
        $repository->updateCaracter($updateCaracter);
        
        $caracter = $repository->findOneById($caracter->getId());
        return $this->json($caracter, 200, [], [
            'groups' => ['caracter.index','caracter.details']
        ]);
    }

}
