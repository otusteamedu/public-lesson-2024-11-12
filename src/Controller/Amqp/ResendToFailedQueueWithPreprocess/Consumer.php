<?php

namespace App\Controller\Amqp\ResendToFailedQueueWithPreprocess;

use App\Controller\Amqp\AbstractConsumer;
use App\DTO\ProcessPendingMessage;
use App\Service\ConsumeMessageService;
use App\Service\MetricsService;
use App\Service\PendingMessageService;
use App\Service\RabbitMqBus;
use App\Type\AmqpExchange;
use App\Type\ConsumeType;

class Consumer extends AbstractConsumer
{
    public function __construct(
        private readonly ConsumeMessageService $messageService,
        private readonly MetricsService        $metricsService,
        private readonly RabbitMqBus           $rabbitMqBus,
        private readonly PendingMessageService $pendingMessageService,
    ) {
    }
    protected function getMessageClass(): string
    {
        return Message::class;
    }

    /**
     * @param Message $message
     */
    protected function handle($message): int
    {
        if ($this->pendingMessageService->hasPendingMessage($message->userId)) {
            return $this->resend($message);
        }
        if (!$this->messageService->tryProcessMessage($message)) {
            return $this->resend($message);
        }

        $this->metricsService->incConsumed($message->userId, ConsumeType::ResendFailed);

        return self::MSG_ACK;
    }

    private function resend(Message $message): int
    {
        $this->pendingMessageService->savePendingMessage($message);
        $this->rabbitMqBus->publishToExchange(
            AmqpExchange::ConsumeTestPending,
            new ProcessPendingMessage($message->userId)
        );

        return self::MSG_ACK;

    }
}
