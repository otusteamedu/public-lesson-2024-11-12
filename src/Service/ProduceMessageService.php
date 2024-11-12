<?php

namespace App\Service;

use App\DTO\MessageInterface;
use App\Entity\AttemptLog;
use App\Entity\PendingMessage;
use App\Type\ProduceType;
use Doctrine\ORM\EntityManagerInterface;

class ProduceMessageService
{
    public const USER_ID = 4;
    private const PUBLISH_SUCCESS_RATE_PERCENT = 40;

    private const BUSINESS_LOGIC_OPERATION_LENGTH_MICROSECONDS = 500_000;
    private const RABBIT_MQ_TIMEOUT_MICROSECONDS = 2_000_000;

    public function __construct(
        private readonly MetricsService $metricsService,
        private readonly PendingMessageService $pendingMessageService,
    ) {
    }

    public function publishMessageInTransaction(MessageInterface $message): void
    {
        $success = false;
        do {
            // emulate database operation from business-logic
            usleep(self::BUSINESS_LOGIC_OPERATION_LENGTH_MICROSECONDS);
            $success = $this->emulateRabbitMQPublishWithTimeout();
        } while ($success);
        $this->metricsService->incProduced(ProduceType::Transaction);
    }

    public function publishMessageInOutbox(MessageInterface $message): void
    {
        $pendingMessage = new PendingMessage($message->getUserId(), $message->getMessage(), $message->getNumber());
        $this->pendingMessageService->savePendingMessage($pendingMessage);
        // emulate database operation from business-logic
        usleep(self::BUSINESS_LOGIC_OPERATION_LENGTH_MICROSECONDS);
        $this->metricsService->incProduced(ProduceType::TransactionalOutbox);
    }

    public function emulateRabbitMQPublishWithTimeout(): bool
    {
        if (random_int(0, 99) < self::PUBLISH_SUCCESS_RATE_PERCENT) {
            return true;
        }
        // emulate RabbitMQ timeout
        usleep(self::RABBIT_MQ_TIMEOUT_MICROSECONDS);

        return false;
    }
}
