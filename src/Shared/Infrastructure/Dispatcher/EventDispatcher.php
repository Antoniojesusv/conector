<?php

declare(strict_types=1);
namespace App\Shared\Infrastructure\Dispatcher;

use App\Shared\Domain\Dispatcher\Dispatcher;
use App\Shared\Domain\Dispatcher\Subscriber;

final class EventDispatcher implements Dispatcher
{
    private array $subscribers = [];

    public function __construct()
    {
        $this->subscribers['*'] = [];
    }

    private function registerEvent(string $eventName = '*'): void
    {
        if (!isset($this->subscribers[$eventName])) {
            $this->subscribers[$eventName] = [];
        }
    }

    private function getEventSubscribers(string $eventName = '*'): array
    {
        $this->registerEvent($eventName);

        $group = $this->subscribers[$eventName];
        $all = $this->subscribers['*'];

        return [...$all, ...$group];
    }

    public function subscribe(string $eventName, Subscriber $subscriber): void
    {
        $this->registerEvent($eventName);

        $this->subscribers[$eventName][] = $subscriber;
    }

    public function unSubscribe(string $eventName, Subscriber $subscriber): void
    {
        foreach ($this->getEventSubscribers($eventName) as $key => $sub) {
            if ($sub === $subscriber) {
                unset($this->subscribers[$eventName][$key]);
            }
        }
    }

    public function dispatch(string $eventName, array $payload = []): void
    {
        foreach ($this->getEventSubscribers($eventName) as $subscriber) {
            $subscriber->update($eventName, $payload);
        }
    }
}