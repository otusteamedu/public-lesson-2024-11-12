<?php

namespace App\Type;

enum ConsumeType: string
{
    case Requeue = 'requeue';
    case RequeueFailed = 'requeueFailed';
    case ResendFailed = 'resendFailed';
}
