<?php

declare(strict_types=1);
namespace App\Shared\Domain\Bus\Command;

interface CommandBus
{
    public function register(Command $command, callable $handler): void;

    public function dispatch(Command $command): void;
}
