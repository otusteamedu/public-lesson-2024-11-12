<?php

namespace App\DTO;

interface MessageInterface
{
    public function getUserId(): int;

    public function getMessage(): string;

    public function getNumber(): int;
}
