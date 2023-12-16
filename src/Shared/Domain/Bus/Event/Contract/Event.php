<?php

declare(strict_types=1);

namespace App\Shared\Domain\Bus\Event\Contract;

use DateTimeInterface;

interface Event
{
    public function occurredOn(): DateTimeInterface;
    public function metadata(): array;
    public function payload(): array;
}
