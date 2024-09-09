<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class RegistrationController extends AbstractController
{
    private $jwtManager;
    private $tokenStorageInterface;

    public function __construct(JWTTokenManagerInterface $jwtManager, TokenStorageInterface $tokenStorageInterface)
    {
        $this->jwtManager = $jwtManager;
        $this->tokenStorageInterface = $tokenStorageInterface;
    }

    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request, UserRepository $repository, SerializerInterface $serializer, UserPasswordHasherInterface $passwordHasher): Response
{
    try {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        $plaintextPassword = $user->getPassword();

        // Vérifier si l'email est déjà pris
        $existingUser = $repository->findOneBy(['username' => $user->getUsername()]);
        if ($existingUser) {
            return new Response(
                json_encode(['error' => 'Cet e-mail est déjà utilisé.']),
                Response::HTTP_CONFLICT, // HTTP 409 Conflict
                ['Content-Type' => 'application/json']
            );
        }

         // Vérifier si le pseudo est déjà pris
        $existingPseudo = $repository->findOneBy(['pseudo' => $user->getPseudo()]);
        if ($existingPseudo) {
            return new Response(
                json_encode(['error' => 'Pseudo déjà utilisé.']),
                Response::HTTP_CONFLICT, // HTTP 409 Conflict
                ['Content-Type' => 'application/json']
            );
        }

        // Hash du mot de passe
        $hashedPassword = $passwordHasher->hashPassword($user, $plaintextPassword);
        $user->setPassword($hashedPassword);

        // Sauvegarde de l'utilisateur
        $repository->addUser($user);

        // Génération du token JWT
        $jwt = $this->jwtManager->create($user);

        $responseData = [
            'user' => $serializer->serialize($user, 'json', ['groups' => ['user.details']]),
            'token' => $jwt,
        ];

        return new Response(
            json_encode($responseData),
            Response::HTTP_CREATED,
            ['Content-Type' => 'application/json']
        );
    } catch (\Exception $e) {
        // Attraper toutes les autres erreurs et renvoyer une réponse JSON
        return new Response(
            json_encode(['error' => 'Erreur lors de l\'inscription : ' . $e->getMessage()]),
            Response::HTTP_BAD_REQUEST,
            ['Content-Type' => 'application/json']
        );
    }
}

}
