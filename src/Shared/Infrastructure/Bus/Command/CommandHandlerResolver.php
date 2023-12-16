<?php

declare(strict_types=1);
namespace App\Shared\Infrastructure\Bus\Command;

use App\Shared\Domain\Bus\Command\Contract\Command;
use App\Shared\Domain\Bus\Command\Contract\CommandHandler;
use Psr\Container\ContainerInterface;
use App\Shared\Domain\Bus\Command\Contract\CommandHandlerResolver as CommandHandlerResolverContract;

final class CommandHandlerResolver implements CommandHandlerResolverContract
{
    public function __construct(
        private ContainerInterface $locator,
    ) {
        $this->locator = $locator;
    }

    public function getHandlerFor(Command $command): CommandHandler
    {
        $id = $this->getLocatorId($command);

        if (!$this->locator->has($id)) {
            throw new \Exception('Locator id was not found in service locator');
        }

        $handler = $this->locator->get($id);

        return $handler;
    }

    private function getLocatorId(Command $command): string
    {
        return $command::class;
    }
}