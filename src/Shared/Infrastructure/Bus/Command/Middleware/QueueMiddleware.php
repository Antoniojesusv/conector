<?php

declare(strict_types=1);
namespace App\Shared\Infrastructure\Bus\Command\Middleware;

use App\Shared\Domain\Bus\Middleware\Contract\MiddlewareBase;
use \App\Shared\Domain\Bus\Contract\Message;

final class QueueMiddleware extends MiddlewareBase
{
    private array $queue = [];
    private bool $isDispatching = false;
    public function __invoke(Message $message, $next = null): mixed
    {
        $this->queue[] = $message;

        if (!$this->isDispatching) {
            $this->isDispatching = true;

            try {
                while ($message = array_shift($this->queue)) {
                    return parent::handle($message, $next);
                }
            } finally {
                $this->isDispatching = false;
            }
        }

        return null;
    }
}