<?php

namespace App\Service;

use App\DTO\MessageInterface;
use App\Entity\PendingMessage;
use Doctrine\ORM\EntityManagerInterface;

class PendingMessageService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function savePendingMessage(MessageInterface $message): void
    {
        $pendingMessage = new PendingMessage(
            $message->getUserId(),
            $message->getMessage(),
            $message->getNumber(),
        );
        $this->entityManager->persist($pendingMessage);
        $this->entityManager->flush();
    }

    /**
     * @return PendingMessage[]
     */
    public function getPendingMessages(int $userId): array
    {
        $pendingMessageRepository = $this->entityManager->getRepository(PendingMessage::class);

        return $pendingMessageRepository->findBy(['userId' => $userId], ['number' => 'asc']);
    }

    public function hasPendingMessage(int $userId): bool
    {
        $pendingMessageRepository = $this->entityManager->getRepository(PendingMessage::class);

        return $pendingMessageRepository->count(['userId' => $userId]) > 0;
    }

    public function removePendingMessage(PendingMessage $pendingMessage): void
    {
        $this->entityManager->remove($pendingMessage);
        $this->entityManager->flush();
    }
}
