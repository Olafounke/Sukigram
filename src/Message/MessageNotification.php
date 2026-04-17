<?php

namespace App\Message;

class MessageNotification
{
    public function __construct(
        private int $recipientId,
        private string $senderUsername,
        private string $content
    ) {
    }

    public function getRecipientId(): int
    {
        return $this->recipientId;
    }

    public function getSenderUsername(): string
    {
        return $this->senderUsername;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
