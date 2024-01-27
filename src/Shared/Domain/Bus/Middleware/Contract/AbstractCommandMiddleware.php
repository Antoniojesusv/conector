<?php

declare(strict_types=1);

namespace App\Shared\Domain\Bus\Middleware\Contract;

use App\Shared\Domain\Bus\Contract\Message;

abstract class AbstractCommandMiddleware implements Middleware
{
    /**
     * Summary of handle
     * @param \App\Shared\Domain\Bus\Contract\Message $message
     * @param \Closure|null $next
     * @return void
     */
    public function handle(Message $message, \Closure $next = null): void
    {
        if (!is_null($next)) {
            $next($message);
        }
    }
}