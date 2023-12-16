<?php

declare(strict_types=1);

namespace App\Shared\Domain\Bus\Contract;

interface Message
{
    public function id(): string;
    public function getMessageType(): string;
}