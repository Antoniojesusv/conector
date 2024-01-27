<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus;


use App\Shared\Domain\Bus\Contract\BusHandler;
use App\Shared\Domain\Bus\Contract\Message;
use App\Shared\Domain\Bus\Contract\Resolver;
use Psr\Container\ContainerInterface;

class HandlerResolver implements Resolver
{
    public function __construct(
        private ContainerInterface $locator,
    ) {
        $this->locator = $locator;
    }

    public function getHandlerFor(Message $message): BusHandler
    {
        $id = $this->getLocatorId($message);

        if (!$this->locator->has($id)) {
            throw new \Exception('Locator id was not found in service locator');
        }

        $handler = $this->locator->get($id);

        return $handler;
    }

    private function getLocatorId(Message $message): string
    {
        return $message::class;
    }
}