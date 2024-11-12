<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class AttemptLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'integer')]
    private int $attemptsCount = 0;

    public function __construct(
        #[ORM\Column(type: 'integer')]
        private readonly int $userId,
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

    public function getAttemptsCount(): int
    {
        return $this->attemptsCount;
    }

    public function incAttemptsCount(): void
    {
        $this->attemptsCount++;
    }
}
