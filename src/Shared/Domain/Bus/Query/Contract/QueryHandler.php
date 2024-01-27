<?php

declare(strict_types=1);
namespace App\Shared\Domain\Bus\Query\Contract;

use App\Shared\Domain\Bus\Contract\BusHandler;

interface QueryHandler extends BusHandler
{
    public function __invoke(Query $query): mixed;
}