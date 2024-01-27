<?php

declare(strict_types=1);
namespace App\Shared\Infrastructure\Bus\Command\Middleware;

use \App\Shared\Domain\Bus\Contract\Message;
use App\Shared\Domain\Bus\Middleware\Contract\AbstractCommandMiddleware;
use App\Shared\Domain\Dispatcher\Dispatcher;

final class EventDispatcherMiddleware extends AbstractCommandMiddleware
{

    public function __construct(private Dispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(Message $message, $next = null): void
    {
        $this->eventDispatcher->dispatch($message::class, ['payload' => 'test']);

        parent::handle($message, $next);
    }
}