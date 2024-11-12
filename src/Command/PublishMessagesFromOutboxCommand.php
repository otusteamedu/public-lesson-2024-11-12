<?php

namespace App\Command;

use App\DTO\ConsumeTestMessage;
use App\Service\ConsumeMessageService;
use App\Service\MetricsService;
use App\Service\PendingMessageService;
use App\Service\ProduceMessageService;
use App\Service\RabbitMqBus;
use App\Type\AmqpExchange;
use App\Type\ProduceType;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'test:outbox:publish')]
class PublishMessagesFromOutboxCommand extends BaseCommand
{
    private const SLEEP_INTERVAL_MICROSECONDS = 10_000;

    public function __construct(
        private readonly PendingMessageService $pendingMessageService,
        private readonly ProduceMessageService $produceMessageService,
        private readonly MetricsService $metricsService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        while (true) {
            $pendingMessages = $this->pendingMessageService->getPendingMessages(ProduceMessageService::USER_ID);
            foreach ($pendingMessages as $message) {
                if (!$this->produceMessageService->emulateRabbitMQPublishWithTimeout()) {
                    break;
                }
                $this->pendingMessageService->removePendingMessage($message);
                $this->metricsService->incProduced(ProduceType::OutboxPublish);
            }
            usleep(self::SLEEP_INTERVAL_MICROSECONDS);
        }

        return self::SUCCESS;
    }
}
