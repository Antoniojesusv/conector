<?php

declare(strict_types=1);

namespace App\Shared\Domain\Dispatcher;

interface Dispatcher
{
    public function subscribe(string $eventName, Subscriber $subscriber): void;
    public function unSubscribe(string $eventName, Subscriber $subscriber): void;
    public function dispatch(string $eventName, array $payload = []): void;
}