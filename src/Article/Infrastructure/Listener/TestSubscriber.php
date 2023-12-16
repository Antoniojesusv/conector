<?php

declare(strict_types=1);
namespace App\Article\Infrastructure\Listener;

use App\Shared\Domain\Dispatcher\Subscriber;

final class TestSubscriber implements Subscriber
{
    public function update(string $eventName, array $payload = []): void
    {
        $result = $payload;
    }
}