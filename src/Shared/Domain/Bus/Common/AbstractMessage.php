<?php

declare(strict_types=1);

namespace App\Shared\Domain\Bus\Common;

use App\Shared\Domain\Bus\Contract\Message;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

abstract class AbstractMessage implements Message
{
    protected readonly string $uuid;
    protected readonly DateTimeImmutable $createdAt;

    const MESSAGE_TYPE = "Message";

    public function __construct()
    {
        $this->uuid = $this->random();
        $this->createdAt = new DateTimeImmutable();
    }

    public function uuid(): string
    {
        return $this->uuid;
    }

    public function getType(): string
    {
        return self::MESSAGE_TYPE;
    }

    private function random(): string
    {
        return Uuid::uuid4()->toString();
    }
}