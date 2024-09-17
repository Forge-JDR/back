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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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

    #[Route('/api/me', name: 'app_user_update', methods: ['PUT'])]
    public function updateUser(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, SerializerInterface $serializer): JsonResponse
    {
        $user = $this->tokenStorageInterface->getToken()->getUser();
        //dd($user);
        $this->denyAccessUnlessGranted('USER_EDIT', $user);
        $user = $serializer->deserialize($request->getContent(), User::class, 'json', ['object_to_populate' => $user]);
        
        
        $data = json_decode($request->getContent(), true);
        
        if (isset($data['username'])) {
            $user->setUsername($data['username']);
        }

        if (isset($data['password'])) {
            $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);
        }

        $userRepository->updateUser($user);

        return $this->json($user, 200, [], [
            'groups' => ['user.details']
        ]);
    }

    #[Route('/api/me', name: 'app_user_delete', methods: ['DELETE'])]
    public function deleteUser(UserRepository $userRepository): JsonResponse
    {
        $user = $this->tokenStorageInterface->getToken()->getUser();
        $this->denyAccessUnlessGranted('USER_DELETE', $user);
        $userRepository->removeUser($user);

        return $this->json(['message' => 'User deleted successfully'], 200);
    }

}



    
