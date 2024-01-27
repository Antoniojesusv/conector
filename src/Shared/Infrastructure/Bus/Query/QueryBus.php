<?php

declare(strict_types=1);
namespace App\Shared\Infrastructure\Bus\Query;

use App\Shared\Domain\Bus\Common\AbstractBus;
use App\Shared\Domain\Bus\Query\Contract\Query;
use App\Shared\Domain\Bus\Query\Contract\QueryBus as QueryBusInterface;

final class QueryBus extends AbstractBus implements QueryBusInterface
{
    public function dispatch(Query $query): mixed
    {
        $chain = $this->createChain();
        return $chain($query);
    }
}