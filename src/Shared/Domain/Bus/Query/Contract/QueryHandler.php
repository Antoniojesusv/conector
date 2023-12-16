<?php

declare(strict_types=1);
namespace App\Shared\Domain\Bus\Query\Contract;

interface QueryHandler
{
    public function __invoke(Query $message): mixed;
}