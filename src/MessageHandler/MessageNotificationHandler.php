<?php

namespace App\MessageHandler;

use App\Message\MessageNotification;
use App\Repository\UserRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class MessageNotificationHandler
{
    public function __construct(
        private MailerInterface $mailer,
        private UserRepository $userRepository
    ) {
    }

    public function __invoke(MessageNotification $message)
    {
        $recipient = $this->userRepository->find($message->getRecipientId());

        if (!$recipient) {
            return;
        }

        $email = (new Email())
            ->from('noreply@sukigram.com')
            ->to($recipient->getEmail())
            ->subject('Nouveau message de @' . $message->getSenderUsername())
            ->text('Vous avez reçu un nouveau message de @' . $message->getSenderUsername() . ' : ' . $message->getContent());

        $this->mailer->send($email);
    }
}
