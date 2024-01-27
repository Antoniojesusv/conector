<?php

declare(strict_types=1);

namespace App\Shared\Domain\Bus\Middleware\Contract;

use App\Shared\Domain\Bus\Contract\Message;
use Closure;

interface CommandMiddleware extends Middleware
{
    public function __invoke(Message $message, Closure $next = null): void;
}