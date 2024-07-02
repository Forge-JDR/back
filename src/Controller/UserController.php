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
use App\Security\Voter\UserVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserController extends AbstractController
{

    private $serializer;
    private $jwtManager;
    private $tokenStorageInterface;

    public function __construct(private Security $security,SerializerInterface $serializer, TokenStorageInterface $tokenStorageInterface, JWTTokenManagerInterface $jwtManager)
    {
        $this->serializer = $serializer;
        $this->jwtManager = $jwtManager;
        $this->tokenStorageInterface = $tokenStorageInterface;
    }


   
    #[Route ('/api/me', name: 'app_user_get_info', methods: ['GET'])]
    public function getUserInfo(): JsonResponse
    {
        $user = $this->tokenStorageInterface->getToken()->getUser();
        //$decodedJwtToken = $this->jwtManager->decode($this->tokenStorageInterface->getToken());

        return $this->json(
           $user,200,[], 
              ['groups' => ['user.details']]
        );
    }

}