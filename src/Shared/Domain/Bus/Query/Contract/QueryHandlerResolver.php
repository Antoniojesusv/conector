<?php

declare(strict_types=1);
namespace App\Shared\Domain\Bus\Query\Contract;

interface QueryHandlerResolver
{
    public function getHandlerFor(Query $command): QueryHandler;
}