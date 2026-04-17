<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\UserRepository;
use App\Repository\PostRepository;
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
        $post->setAuthor($this->getUser());

        $em->persist($post);
        $em->flush();

        $this->addFlash('success', 'Votre post a été publié !');
        return $this->redirectToRoute('app_home');
    }

    return $this->render('post/new.html.twig', [
        'postForm' => $form->createView(),
    ]);
}
    #[Route('/feed', name: 'app_post_feed')]
    #[IsGranted('ROLE_USER')] 
    public function feed(PostRepository $postRepository): Response
    {
    /** @var User $user */
        $user = $this->getUser();
        $following = $user->getFollowing();

    if ($following->isEmpty()) {
        return $this->render('post/feed.html.twig', [
            'posts' => [],
            'message' => 'Vous ne suivez encore personne. Explorez les profils pour voir du contenu ici !'
        ]);
    }

    $posts = $postRepository->findBy(
        ['author' => $following->toArray()],
        ['createdAt' => 'DESC']
    );

    return $this->render('post/feed.html.twig', [
        'posts' => $posts
    ]);
}

    #[Route('/{id}/delete', name: 'app_post_delete', methods: ['POST', 'GET'])]
    public function delete(Post $post, EntityManagerInterface $entityManager): Response
    {
    
        $this->denyAccessUnlessGranted('POST_DELETE', $post);

        $entityManager->remove($post);
        $entityManager->flush();

        $this->addFlash('success', 'Post supprimé avec succès.');
        return $this->redirectToRoute('app_home');
    }

}