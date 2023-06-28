<?php
declare(strict_types=1);
namespace App\Shared\Domain\Bus\Event;

abstract class DomainEvent
{
    private readonly string $occurredOn;

    public function __construct()
    {
    }
}
