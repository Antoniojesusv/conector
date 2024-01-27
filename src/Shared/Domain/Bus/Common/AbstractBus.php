<?php

declare(strict_types=1);

namespace App\Shared\Domain\Bus\Common;

use App\Shared\Domain\Bus\Middleware\Exception\ObjectIsNotMiddlewareInstance;
use App\Shared\Infrastructure\Bus\HandlerResolver;

abstract class AbstractBus
{
    public function __construct(
        private HandlerResolver $handlerResolver,
        private array $middlewares = []
    ) {
        $this->handlerResolver = $handlerResolver;
        $this->middlewares = $middlewares;
    }

    protected function createChain(): \Closure
    {
        $lastMiddleware = fn($message) => $this->handlerResolver->getHandlerFor($message)($message);
        $middlewareList = [...$this->middlewares];

        while ($middleware = array_pop($middlewareList)) {
            if (($middleware instanceof Middleware)) {
                throw new ObjectIsNotMiddlewareInstance($middleware);
            }
            $lastMiddleware = fn($message) => $middleware($message, $lastMiddleware);
        }

        return $lastMiddleware;
    }
}