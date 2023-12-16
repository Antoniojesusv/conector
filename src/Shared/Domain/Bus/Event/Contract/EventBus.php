<?php

declare(strict_types=1);
namespace App\Shared\Domain\Bus\Event\Contract;

use App\Shared\Domain\Bus\Event\DomainEvent;

interface EventBus
{
    public function dispatch(DomainEvent $event): void;
}