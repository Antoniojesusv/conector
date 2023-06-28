<?php

declare(strict_types=1);
namespace App\Shared\Infrustructure\Bus\Command;

use App\Shared\Domain\Bus\Command\Command;
use App\Shared\Domain\Bus\Command\CommandBus;

final class CommandBusSync implements CommandBus
{
    private array $handlers = [];

    public function register(Command $command, callable $handler): void
    {
        $this->handlers[$command::class] = $handler;
    }

    public function dispatch(Command $command): void
    {
        $this->handlers[$command::class]($command);
    }
}
