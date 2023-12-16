<?php

declare(strict_types=1);
namespace App\Shared\Infrastructure\Bus\Query;

use App\Shared\Domain\Bus\Query\Contract\Query;
use App\Shared\Domain\Bus\Query\Contract\QueryHandler;
use Psr\Container\ContainerInterface;
use App\Shared\Domain\Bus\Query\Contract\QueryHandlerResolver as QueryHandlerResolverContract;

final class QueryHandlerResolver implements QueryHandlerResolverContract
{
    public function __construct(
        private ContainerInterface $locator,
    ) {
        $this->locator = $locator;
    }

    public function getHandlerFor(Query $command): QueryHandler
    {
        $id = $this->getLocatorId($command);

        if (!$this->locator->has($id)) {
            throw new \Exception('Locator id was not found in service locator');
        }

        $handler = $this->locator->get($id);

        return $handler;
    }

    private function getLocatorId(Query $command): string
    {
        return $command::class;
    }
}