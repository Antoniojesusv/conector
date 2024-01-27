<?php

declare(strict_types=1);
namespace App\Shared\Domain\Bus\Command\Contract;

interface CommandBus
{
    public function dispatch(Command $command): void;
}