<?php

declare(strict_types=1);
namespace App\Shared\Infrastructure\Dispatcher\FactoryMethod;

use App\Shared\Infrastructure\Dispatcher\EventDispatcher;

final class EventDispatcherCommandFactory
{
    public function create(array $subscribers): EventDispatcher
    {
        $eventDispatcher = new EventDispatcher();

        foreach ($subscribers as $eventName => $subscriber) {
            $eventDispatcher->subscribe($eventName, $subscriber);
        }

        return $eventDispatcher;
    }
}