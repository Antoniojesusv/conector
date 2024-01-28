<?php

declare(strict_types=1);

namespace App\Shared\Domain\Bus\Middleware\Contract;

use App\Shared\Domain\Bus\Contract\Message;

abstract class AbstractQueryMiddleware implements Middleware
{
    /**
     * Summary of handle
     * @param \App\Shared\Domain\Bus\Contract\Message $message
     * @param \Closure|null $next
     * @return mixed
     */
    public function handle(Message $message, \Closure $next = null): mixed
    {
        if (!is_null($next)) {
            return $next($message);
        }

        return null;
    }
}