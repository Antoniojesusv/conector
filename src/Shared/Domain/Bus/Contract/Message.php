<?php

declare(strict_types=1);

namespace App\Shared\Domain\Bus\Contract;

interface Message
{
    public function uuid(): string;
    public function getType(): string;
}