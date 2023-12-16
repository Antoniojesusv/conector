<?php

declare(strict_types=1);

namespace App\Shared\Domain\Bus\Contract;

use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

abstract class BaseMessage implements Message
{
    protected readonly string $uuid;
    protected readonly DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->uuid = $this->random();
        $this->createdAt = new DateTimeImmutable();
    }

    private function random(): string
    {
        return Uuid::uuid4()->toString();
    }
}