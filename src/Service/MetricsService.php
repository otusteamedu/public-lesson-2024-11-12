<?php

namespace App\Service;

use App\Type\ConsumeType;
use App\Type\ProduceType;
use Domnikl\Statsd\Client;
use Domnikl\Statsd\Connection\UdpSocket;

class MetricsService
{
    private const DEFAULT_SAMPLE_RATE = 1.0;
    private const CONSUME_TEMPLATE = 'user%d_%s_processed';
    private const ERROR_TEMPLATE = 'user%d_error';
    private const PRODUCE_TEMPLATE = '%s_processed';

    private Client $client;

    public function __construct(string $host, int $port, string $namespace)
    {
        $connection = new UdpSocket($host, $port);
        $this->client = new Client($connection, $namespace);
    }

    public function incConsumed(int $userId, ConsumeType $consumeType): void
    {
        $this->increment(sprintf(self::CONSUME_TEMPLATE, $userId, $consumeType->value));
    }

    public function incError(int $userId): void
    {
        $this->increment(sprintf(self::ERROR_TEMPLATE, $userId));
    }

    public function incProduced(ProduceType $produceType): void
    {
        $this->increment(sprintf(self::PRODUCE_TEMPLATE, $produceType->value));
    }

    private function increment(string $key, ?float $sampleRate = null, ?array $tags = null): void
    {
        $this->client->increment($key, $sampleRate ?? self::DEFAULT_SAMPLE_RATE, $tags ?? []);
    }
}
