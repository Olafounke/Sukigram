<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class SocialController extends AbstractController
{
    #[Route('/like/post/{id}', name: 'app_post_like')]
    public function like(Post $post, EntityManagerInterface $em, Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) return $this->redirectToRoute('app_login');

        if ($post->getLikedBy()->contains($user)) {
            $post->removeLikedBy($user);
        } else {
            $post->addLikedBy($user);
            
            if ($post->getAuthor() !== $user) {
                $notif = new Notification();
                $notif->setReceptor($post->getAuthor());
                $notif->setSender($user);
                $notif->setContent("a aimé votre post");
                $em->persist($notif);
            }
        }

        $em->flush();
        $referer = $request->headers->get('referer');
        return $this->redirect($referer ?: $this->generateUrl('app_home'));
    }

    #[Route('/follow/user/{id}', name: 'app_user_follow')]
    public function follow(User $targetUser, EntityManagerInterface $em): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if (!$currentUser || $currentUser === $targetUser) return $this->redirectToRoute('app_home');

        if ($targetUser->getFollowers()->contains($currentUser)) {
            $targetUser->removeFollower($currentUser);
        } else {
            $targetUser->addFollower($currentUser);

       
            $notif = new Notification();
            $notif->setReceptor($targetUser);
            $notif->setSender($currentUser);
            $notif->setContent("a commencé à vous suivre");
            $em->persist($notif);
        }

        $em->flush();
        return $this->redirectToRoute('app_profile', ['username' => $targetUser->getUsername()]);
    }

    #[Route('/notifications', name: 'app_notifications')]
    public function notifications(NotificationRepository $notifRepo, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) return $this->redirectToRoute('app_login');

        $notifications = $notifRepo->findBy(
            ['receptor' => $user],
            ['createdAt' => 'DESC']
        );

      
        foreach ($notifications as $notif) {
            $notif->setIsRead(true);
        }
        $em->flush();

        return $this->render('social/notifications.html.twig', [
            'notifications' => $notifications
        ]);
    }
}