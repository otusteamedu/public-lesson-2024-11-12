<?php

namespace App\Controller\Amqp\Requeue;

use App\Controller\Amqp\AbstractConsumer;
use App\Service\ConsumeMessageService;
use App\Service\MetricsService;
use App\Type\ConsumeType;

class Consumer extends AbstractConsumer
{
    public function __construct(
        private readonly ConsumeMessageService $messageService,
        private readonly MetricsService        $metricsService,
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
            return self::MSG_REJECT_REQUEUE;
        }

        $this->metricsService->incConsumed($message->userId, $this->consumeType);

        return self::MSG_ACK;
    }
}
