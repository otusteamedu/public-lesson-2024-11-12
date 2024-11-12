<?php

namespace App\Controller\Amqp\ResendToFailedQueueWithPreprocess;

use App\DTO\MessageInterface;

class Message implements MessageInterface
{
    public function __construct(
        public readonly int $userId,
        public readonly string $message,
        public readonly int $number,
    ) {
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getNumber(): int
    {
        return $this->number;
    }
}
