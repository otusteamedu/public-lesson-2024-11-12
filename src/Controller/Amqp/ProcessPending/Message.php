<?php

namespace App\Controller\Amqp\ProcessPending;

class Message
{
    public function __construct(
        public readonly int $userId,
    ) {
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
