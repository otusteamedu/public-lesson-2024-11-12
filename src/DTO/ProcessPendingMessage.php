<?php

namespace App\DTO;

class ProcessPendingMessage
{
    public function __construct(
        public readonly int $userId,
    ) {
    }
}
