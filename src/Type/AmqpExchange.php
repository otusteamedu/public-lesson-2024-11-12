<?php

namespace App\Type;

enum AmqpExchange: string
{
    case ConsumeTest = 'consume_test';
    case ConsumeTestFailedRequeue = 'consume_test_failed_requeue';
    case ConsumeTestPending = 'consume_test_pending';
}
