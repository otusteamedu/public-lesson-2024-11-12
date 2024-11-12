<?php

namespace App\Controller\Amqp\ResendToFailedQueue;

use AMQPQueue;
use App\Controller\Amqp\AbstractConsumer;
use App\Service\ConsumeMessageService;
use App\Service\MetricsService;
use App\Service\RabbitMqBus;
use App\Type\AmqpExchange;
use App\Type\ConsumeType;

class Consumer extends AbstractConsumer
{
    public function __construct(
        private readonly ConsumeMessageService $messageService,
        private readonly MetricsService        $metricsService,
        private readonly RabbitMqBus           $rabbitMqBus,
        private readonly AmqpExchange          $failedExchange,
        private readonly ConsumeType           $consumeType,
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
        if (!$this->messageService->tryProcessMessage($message)) {
            $this->rabbitMqBus->publishToExchange($this->failedExchange, $message);

            return self::MSG_ACK;
        }

        $this->metricsService->incConsumed($message->userId, $this->consumeType);

        return self::MSG_ACK;
    }
}
