<?php
namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request, UserRepository $repository, UserPasswordHasherInterface $passwordHasher, JWTTokenManagerInterface $jwtManager): JsonResponse
    {
        $data = $request->toArray();
        if (array_diff(array_keys($data), ['username', 'password', 'pseudo']) ||
            !is_string($data['username'] ?? null) || mb_strlen($data['username']) > 180 || !filter_var($data['username'], FILTER_VALIDATE_EMAIL) ||
            !is_string($data['pseudo'] ?? null) || trim($data['pseudo']) === '' || mb_strlen($data['pseudo']) > 100 ||
            !is_string($data['password'] ?? null) || mb_strlen($data['password']) < 8 || mb_strlen($data['password']) > 4096) {
            return $this->json(['error' => 'E-mail, pseudo et mot de passe valides requis.'], 400);
        }
        $pseudo = trim($data['pseudo']);
        if ($repository->findOneBy(['username' => $data['username']]) || $repository->findOneBy(['pseudo' => $pseudo])) {
            return $this->json(['error' => 'Cet e-mail ou pseudo est déjà utilisé.'], 409);
        }
        $user = new User();
        $user->setUsername($data['username']);
        $user->setPseudo($pseudo);
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
        $repository->addUser($user);
        return $this->json(['token' => $jwtManager->create($user)], 201);
    }
}
