<?php

declare(strict_types=1);
namespace App\Shared\Domain\Bus\Query\Contract;

interface QueryBus
{
    public function dispatch(Query $query): mixed;
}