<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile/{username}', name: 'app_profile')]
    public function index(string $username, UserRepository $userRepository): Response
    {
        
        $user = $userRepository->findOneBy(['username' => $username]);

        
        if (!$user) {
            throw $this->createNotFoundException("L'utilisateur '$username' n'existe pas.");
        }

        return $this->render('profile/profile.html.twig', [
            'user' => $user,
        ]);
    }
}