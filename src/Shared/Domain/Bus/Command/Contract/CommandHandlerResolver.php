<?php

declare(strict_types=1);
namespace App\Shared\Domain\Bus\Command\Contract;

interface CommandHandlerResolver
{
    public function getHandlerFor(Command $command): CommandHandler;
}