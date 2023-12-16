<?php

declare(strict_types=1);
namespace App\Shared\Infrastructure\Bus\Query;

use App\Shared\Domain\Bus\Query\Contract\Query;
use App\Shared\Domain\Bus\Middleware\Exception\ObjectIsNotMiddlewareInstance;
use App\Shared\Domain\Bus\Query\Contract\QueryBus as QueryBusInterface;
use App\Shared\Domain\Bus\Query\Contract\QueryHandlerResolver;

final class QueryBus implements QueryBusInterface
{
    public function __construct(
        private QueryHandlerResolver $queryHandlerResolver,
        private array $middlewares = []
    ) {
        $this->queryHandlerResolver = $queryHandlerResolver;
        $this->middlewares = $middlewares;
    }

    private function createChain(): \Closure
    {
        $lastMiddleware = fn($query) => $this->queryHandlerResolver->getHandlerFor($query)($query);
        $middlewareList = [...$this->middlewares];

        while ($middleware = array_pop($middlewareList)) {
            if (($middleware instanceof Middleware)) {
                throw new ObjectIsNotMiddlewareInstance($middleware);
            }
            $lastMiddleware = fn($query) => $middleware($query, $lastMiddleware);
        }

        return $lastMiddleware;
    }

    public function dispatch(Query $query): mixed
    {
        $chain = $this->createChain();
        return $chain($query);
    }
}