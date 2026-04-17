<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PostController extends AbstractController
{

    #[Route('/post/new', name: 'app_post_new')]
    public function new(Request $request, EntityManagerInterface $em, UserRepository $userRepo): Response
        {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        
        $tempUser = $userRepo->findAll()[0]; 
        $post->setAuthor($tempUser);

        $em->persist($post);
        $em->flush();

        $this->addFlash('success', 'Votre post a été publié !');
        return $this->redirectToRoute('app_home');
    }

    return $this->render('post/new.html.twig', [
        'postForm' => $form->createView(),
    ]);
}
}