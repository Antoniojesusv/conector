<?php

declare(strict_types=1);

namespace App\Shared\Domain\Bus\Event\Contract;

use DateTimeImmutable;

abstract class DomainEvent implements Event
{
    protected array $metadata;
    protected readonly DateTimeImmutable $occurredOn;

    public function __construct(array $metadata)
    {
        $this->metadata = $metadata;
        $this->ocurredOn = new DateTimeImmutable();
    }
}
