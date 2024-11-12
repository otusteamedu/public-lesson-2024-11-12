<?php

namespace App\Command;

use App\DTO\ConsumeTestMessage;
use App\Service\ConsumeMessageService;
use App\Service\RabbitMqBus;
use App\Type\AmqpExchange;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'test:consume')]
class ConsumeTestCommand extends BaseCommand
{
    private const MESSAGES_COUNT = 100;
    private const MESSAGE_LENGTH = 20;

    public function __construct(
        private readonly ConsumeMessageService $messageService,
        private readonly RabbitMqBus           $rabbitMqBus,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $messages = [];
        foreach (ConsumeMessageService::USER_SUCCESS_RATE_PERCENT as $userId => $_) {
            $messages[] = array_map(
                static fn($callable) => $callable(),
                array_fill(
                    0,
                    self::MESSAGES_COUNT,
                    static fn() => new ConsumeTestMessage($userId, base64_encode(random_bytes(self::MESSAGE_LENGTH)))
                )
            );
        }
        $messages = array_merge(...$messages);
        shuffle($messages);
        $counters = array_fill_keys(array_keys(ConsumeMessageService::USER_SUCCESS_RATE_PERCENT), $this->messageService->getMaxNumber() + 1);
        $progressBar = new ProgressBar($output, count($messages));
        /** @var ConsumeTestMessage $message */
        foreach ($messages as $message) {
            $message->number = $counters[$message->userId]++;
            $this->rabbitMqBus->publishToExchange(AmqpExchange::ConsumeTest, $message);
            $progressBar->advance();
        }
        $progressBar->finish();
        $output->writeln('');

        return self::SUCCESS;
    }
}
