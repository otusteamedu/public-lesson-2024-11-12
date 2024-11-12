<?php

namespace App\Service;

use App\DTO\MessageInterface;
use App\Entity\AttemptLog;
use Doctrine\ORM\EntityManagerInterface;

class ConsumeMessageService
{
    public const USER_SUCCESS_RATE_PERCENT = [
        1 => 25,
        2 => 40,
        3 => 100
    ];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MetricsService $metricsService,
    ) {
    }

    public function tryProcessMessage(MessageInterface $message): bool
    {
        $attemptLogRepository = $this->entityManager->getRepository(AttemptLog::class);
        /** @var AttemptLog $attemptLog */
        $attemptLog = $attemptLogRepository->findOneBy(
            ['userId' => $message->getUserId(), 'message' => $message->getMessage()]
        );

        if ($attemptLog === null) {
            if ($this->getMaxNumber($message->getUserId()) >= $message->getNumber()) {
                $this->metricsService->incError($message->getUserId());
            }
            $attemptLog = new AttemptLog($message->getUserId(), $message->getMessage(), $message->getNumber());
            $this->entityManager->persist($attemptLog);
        } else {
            $attemptLog->incAttemptsCount();
        }
        $this->entityManager->flush();
        usleep(250_000);

        return random_int(0, 99) < self::USER_SUCCESS_RATE_PERCENT[$message->getUserId()];
    }

    public function getMaxNumber(?int $userId = null): int
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select($qb->expr()->max('a.number'))
            ->from(AttemptLog::class, 'a');
        if ($userId !== null) {
            $qb->andWhere($qb->expr()->eq('a.userId', ':userId'))
                ->setParameter('userId', $userId);
        }

        return $qb->getQuery()->getSingleScalarResult() ?? 0;
    }
}
