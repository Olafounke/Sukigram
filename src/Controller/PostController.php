<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Service\FileUploader;
use App\Repository\UserRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class PostController extends AbstractController
{

    #[Route('/post/new', name: 'app_post_new')]
    public function new(Request $request, EntityManagerInterface $em, FileUploader $fileUploader): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $post->setImageUrl($fileUploader->upload($imageFile));
            }

            $post->setAuthor($this->getUser());

            $em->persist($post);
            $em->flush();

            // $cache->delete('user_feed_' . $this->getUser()->getId());

            $this->addFlash('success', 'Votre post a été publié !');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('post/new.html.twig', [
            'postForm' => $form->createView(),
        ]);
    }

    #[Route('/feed', name: 'app_post_feed')]
    public function feed(PostRepository $postRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        $posts = $postRepository->findFeedForUser($user);

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