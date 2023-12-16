<?php

declare(strict_types=1);
namespace App\Shared\Infrastructure\Bus\Command;

use App\Shared\Domain\Bus\Command\Contract\Command;
use App\Shared\Domain\Bus\Command\Contract\CommandBus;
use App\Shared\Domain\Bus\Middleware\Exception\ObjectIsNotMiddlewareInstance;

final class CommandBusSync implements CommandBus
{
    public function __construct(
        private CommandHandlerResolver $commandHandlerResolver,
        private array $middlewares = []
    ) {
        $this->commandHandlerResolver = $commandHandlerResolver;
        $this->middlewares = $middlewares;
    }

    private function createChain(): \Closure
    {
        $lastMiddleware = fn($command) => $this->commandHandlerResolver->getHandlerFor($command)($command);
        $middlewareList = [...$this->middlewares];

        while ($middleware = array_pop($middlewareList)) {
            if (($middleware instanceof Middleware)) {
                throw new ObjectIsNotMiddlewareInstance($middleware);
            }
            $lastMiddleware = fn($command) => $middleware($command, $lastMiddleware);
        }

        return $lastMiddleware;
    }

    public function dispatch(Command $command): mixed
    {
        $chain = $this->createChain();
        return $chain($command);
    }
}