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
        $data = $request->toArray();
        $allowed = ['username', 'pseudo', 'password', 'currentPassword'];
        if (array_diff(array_keys($data), $allowed)) {
            return $this->json(['message' => 'Champ non autorisé.'], 400);
        }
        foreach ($data as $value) {
            if (!is_string($value)) {
                return $this->json(['message' => 'Les champs doivent être des chaînes de caractères.'], 400);
            }
        }
        if (isset($data['username']) && !filter_var($data['username'], FILTER_VALIDATE_EMAIL)) {
            return $this->json(['message' => 'Adresse e-mail invalide.'], 400);
        }
        if (isset($data['pseudo']) && (trim($data['pseudo']) === '' || mb_strlen($data['pseudo']) > 255)) {
            return $this->json(['message' => 'Pseudo invalide.'], 400);
        }
        if (isset($data['password'])) {
            if (!isset($data['currentPassword']) || !$passwordHasher->isPasswordValid($user, $data['currentPassword'])) {
                return $this->json(['message' => 'Mot de passe actuel incorrect.'], 400);
            }
            if (mb_strlen($data['password']) < 8 || mb_strlen($data['password']) > 4096) {
                return $this->json(['message' => 'Le mot de passe doit contenir entre 8 et 4096 caractères.'], 400);
            }
        }
        if (isset($data['username'])) {
            $existing = $userRepository->findOneBy(['username' => $data['username']]);
            if ($existing && $existing->getId() !== $user->getId()) {
                return $this->json(['message' => 'Cet e-mail est déjà utilisé.'], 409);
            }
        }
        if (isset($data['pseudo'])) {
            $existing = $userRepository->findOneBy(['pseudo' => trim($data['pseudo'])]);
            if ($existing && $existing->getId() !== $user->getId()) {
                return $this->json(['message' => 'Pseudo déjà utilisé.'], 409);
            }
        }

        if (isset($data['username'])) {
            $user->setUsername($data['username']);
        }
        if (isset($data['pseudo'])) {
            $user->setPseudo(trim($data['pseudo']));
        }
        if (isset($data['password'])) {
            $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
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



    

