<?php

namespace App\Controller\Amqp\ProcessPending;

use App\Controller\Amqp\AbstractConsumer;
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
        $pendingMessages = $this->pendingMessageService->getPendingMessages($message->getUserId());
        foreach ($pendingMessages as $pendingMessage) {
            if (!$this->messageService->tryProcessMessage($pendingMessage)) {
                $this->rabbitMqBus->publishToExchange(
                    AmqpExchange::ConsumeTestPending,
                    $message
                );

                return self::MSG_ACK;
            }
            $this->metricsService->incConsumed($message->userId, ConsumeType::ResendFailed);
            $this->pendingMessageService->removePendingMessage($pendingMessage);
        }

        return self::MSG_ACK;
    }
}
