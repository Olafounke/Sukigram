<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Service\FileUploader;
use App\Repository\ConversationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\MessageNotification;

class MessageController extends AbstractController
{
    #[Route('/messages', name: 'app_messages')]
    public function index(ConversationRepository $convRepo, UserRepository $userRepo): Response
    {
        $conversations = $convRepo->createQueryBuilder('c')
            ->join('c.participants', 'p')
            ->where('p = :user')
            ->setParameter('user', $this->getUser())
            ->getQuery()->getResult();

        $allUsers = $userRepo->findAll();

        return $this->render('message/index.html.twig', [
            'conversations' => $conversations,
            'users' => $allUsers,
        ]);
    }

    #[Route('/messages/chat/{id}', name: 'app_chat')]
    public function chat(Conversation $conversation, Request $request, EntityManagerInterface $em, FileUploader $fileUploader, MessageBusInterface $bus): Response
    {
        if (!$conversation->getParticipants()->contains($this->getUser())) {
            throw $this->createAccessDeniedException();
        }

        foreach ($conversation->getMessages() as $message) {
            if ($message->getSender() !== $this->getUser()) {
                $message->setIsRead(true); 
            }
        }
        $em->flush();

        if ($request->isMethod('POST')) {
            $content = $request->request->get('content');
            $imageFile = $request->files->get('image');

            if ($content || $imageFile) {
                $msg = new Message();
                $msg->setContent($content ?: '')
                    ->setSender($this->getUser())
                    ->setConversation($conversation)
                    ->setCreatedAt(new \DateTimeImmutable());
                
                if ($imageFile) {
                    $msg->setImageUrl($fileUploader->upload($imageFile));
                }

                $em->persist($msg);
                $em->flush();

                foreach ($conversation->getParticipants() as $participant) {
                    if ($participant !== $this->getUser()) {
                        $bus->dispatch(new MessageNotification(
                            $participant->getId(),
                            $this->getUser()->getUsername(),
                            $msg->getContent()
                        ));
                    }
                }
            }
            
            return $this->redirectToRoute('app_chat', ['id' => $conversation->getId()]);
        }

        return $this->render('message/chat.html.twig', [
            'conversation' => $conversation,
        ]);
    }

    #[Route('/messages/start/{id}', name: 'app_conversation_start', methods: ['POST'])]
    public function start(User $targetUser, EntityManagerInterface $em): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        foreach ($currentUser->getConversations() as $conv) {
            if ($conv->getParticipants()->contains($targetUser) && $conv->getParticipants()->count() === 2) {
                return $this->redirectToRoute('app_chat', ['id' => $conv->getId()]);
            }
        }

        $conversation = new Conversation();
        $conversation->addParticipant($currentUser);
        $conversation->addParticipant($targetUser);

        $em->persist($conversation);
        $em->flush();

        return $this->redirectToRoute('app_chat', ['id' => $conversation->getId()]);
    }

    #[Route('/messages/group/new', name: 'app_conversation_group_new', methods: ['POST'])]
    public function createGroup(Request $request, EntityManagerInterface $em, UserRepository $userRepo): Response
    {
        $name = $request->request->get('name');
        $participantIds = $request->request->all('participants');

        if (!$name || empty($participantIds)) {
            $this->addFlash('error', 'Le nom du groupe et au moins un participant sont requis.');
            return $this->redirectToRoute('app_messages');
        }

        $conversation = new Conversation();
        $conversation->setName($name);
        $conversation->addParticipant($this->getUser());

        foreach ($participantIds as $id) {
            $user = $userRepo->find($id);
            if ($user) {
                $conversation->addParticipant($user);
            }
        }

        $em->persist($conversation);
        $em->flush();

        return $this->redirectToRoute('app_chat', ['id' => $conversation->getId()]);
    }
}