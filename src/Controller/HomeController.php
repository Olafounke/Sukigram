<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{

    #[Route('/', name: 'app_home')]
    public function index(PostRepository $postRepository): Response
{
    return $this->render('home/index.html.twig', [
        'posts' => $postRepository->findBy([], ['createdAt' => 'DESC'], 10),
    ]);
}

#[Route('/profile/{username}', name: 'app_profile')]
public function profile(string $username, UserRepository $userRepository): Response
{
    $user = $userRepository->findOneBy(['username' => $username]);

    if (!$user) {
        throw $this->createNotFoundException("Cet utilisateur n'existe pas.");
    }

    return $this->render('profile/profile.html.twig', [
        'user' => $user,
    ]);
}
}