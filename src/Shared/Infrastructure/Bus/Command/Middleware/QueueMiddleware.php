<?php

declare(strict_types=1);
namespace App\Shared\Infrastructure\Bus\Command\Middleware;


use \App\Shared\Domain\Bus\Contract\Message;
use App\Shared\Domain\Bus\Middleware\Contract\AbstractCommandMiddleware;

final class QueueMiddleware extends AbstractCommandMiddleware
{
    private array $queue = [];
    private bool $isDispatching = false;
    public function __invoke(Message $message, $next = null): void
    {
        $this->queue[] = $message;

        if (!$this->isDispatching) {
            $this->isDispatching = true;

            try {
                while ($message = array_shift($this->queue)) {
                    parent::handle($message, $next);
                }
            } finally {
                $this->isDispatching = false;
            }
        }
    }
}