<?php

namespace App\Command;

use App\DTO\ConsumeTestMessage;
use App\Service\ProduceMessageService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'test:produce')]
class ProduceTestCommand extends BaseCommand
{
    private const MESSAGES_COUNT = 100;
    private const MESSAGE_LENGTH = 20;

    public function __construct(
        private readonly ProduceMessageService $produceMessageService,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->addOption('outbox', 'o', InputOption::VALUE_NONE, 'Use transactional outbox');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $outbox = $input->getOption('outbox') ?? false;

        $messages = array_map(
            static fn($callable) => $callable(),
            array_fill(
                0,
                self::MESSAGES_COUNT,
                static fn() => new ConsumeTestMessage(ProduceMessageService::USER_ID, base64_encode(random_bytes(self::MESSAGE_LENGTH)))
            )
        );
        $counter = 0;
        $progressBar = new ProgressBar($output, count($messages));
        /** @var ConsumeTestMessage $message */
        foreach ($messages as $message) {
            $message->number = $counter++;
            if ($outbox) {
                $this->produceMessageService->publishMessageInOutbox($message);
            } else {
                $this->produceMessageService->publishMessageInTransaction($message);
            }
            $progressBar->advance();
        }

        return self::SUCCESS;
    }
}
