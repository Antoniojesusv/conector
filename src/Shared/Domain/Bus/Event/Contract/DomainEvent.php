<?php

declare(strict_types=1);

namespace App\Shared\Domain\Bus\Event\Contract;

use DateTimeImmutable;

abstract class DomainEvent implements Event
{
    protected readonly array $metadata;
    protected readonly DateTimeImmutable $createdAt;

    public function __construct(array $metadata)
    {
        $this->metadata = $metadata;
        $this->createdAt = new DateTimeImmutable();
    }
}
