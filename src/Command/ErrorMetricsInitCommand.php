<?php

namespace App\Command;

use App\DTO\ConsumeTestMessage;
use App\Service\ConsumeMessageService;
use App\Service\MetricsService;
use App\Service\RabbitMqBus;
use App\Type\AmqpExchange;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'test:error')]
class ErrorMetricsInitCommand extends BaseCommand
{
    public function __construct(
        private readonly MetricsService $metricsService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach (ConsumeMessageService::USER_SUCCESS_RATE_PERCENT as $userId => $_) {
            $this->metricsService->incError($userId);
        }

        return self::SUCCESS;
    }
}
