<?php

declare(strict_types=1);

namespace App\Shared\Domain\Dispatcher;

interface Subscriber
{
    public function update(string $eventName, array $payload = []): void;
}