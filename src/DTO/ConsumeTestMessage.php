<?php

namespace App\DTO;

class ConsumeTestMessage implements MessageInterface
{
    public int $number;

    public function __construct(
        public readonly int $userId,
        public readonly string $message,
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
