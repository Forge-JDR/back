<?php

namespace App\Controller;

use App\Repository\UserRepository;


use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserRepository $repository, SerializerInterface $serializer): Response
    {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        
        $repository->addUser($user);
        $idUser = $user->getId();

        $response = new Response(
            $serializer->serialize($repository->findOneById($idUser) , 'json'),
            Response::HTTP_CREATED,
            ['Content-type' => 'application/json']
         );
         return $response;
    }
}
