<?php

declare(strict_types=1);
namespace App\Shared\Infrastructure\Bus\Command;

use App\Shared\Domain\Bus\Command\Contract\Command;
use App\Shared\Domain\Bus\Command\Contract\CommandBus as CommandBusInterface;
use App\Shared\Domain\Bus\Common\AbstractBus;

final class CommandBus extends AbstractBus implements CommandBusInterface
{
    public function dispatch(Command $command): void
    {
        $chain = $this->createChain();
        $chain($command);
    }
}