<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class UserController extends AbstractController
{

    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    #[Route ('/api/users/{user}', name: 'app_user_get_info', methods: ['GET'])]
    public function getUserInfo(User $user, UserRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $user = $repository->findOneById($user->getId());
        return $this->json(
           $user,200,[], 
              ['groups' => ['user.index','user.details', 'wiki.index']]
        );
    }

}