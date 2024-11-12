<?php

namespace App\Type;

enum ProduceType: string
{
    case Transaction = 'transaction';
    case TransactionalOutbox = 'outbox';
    case OutboxPublish = 'publish';
}
