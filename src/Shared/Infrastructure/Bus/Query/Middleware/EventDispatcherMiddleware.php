<?php

declare(strict_types=1);
namespace App\Shared\Infrastructure\Bus\Query\Middleware;

use App\Shared\Domain\Bus\Middleware\Contract\MiddlewareBase;
use \App\Shared\Domain\Bus\Contract\Message;
use App\Shared\Domain\Dispatcher\Dispatcher;

final class EventDispatcherMiddleware extends MiddlewareBase
{

    public function __construct(private Dispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(Message $message, $next = null): mixed
    {
        $this->eventDispatcher->dispatch($message::class, ['payload' => 'test']);

        return parent::handle($message, $next);
    }
}