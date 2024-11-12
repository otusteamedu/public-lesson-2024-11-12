<?php

namespace App\Entity;

use App\DTO\MessageInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class PendingMessage implements MessageInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    public function __construct(
        #[ORM\Column(type: 'integer')]
        private readonly string $userId,
        #[ORM\Column(type: 'string')]
        private readonly string $message,
        #[ORM\Column(type: 'integer')]
        private readonly int $number,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
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
