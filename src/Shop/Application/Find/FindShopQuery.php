<?php

declare(strict_types=1);
namespace App\Synchronisation\Application\Update;

use App\Shared\Domain\Bus\Query\Query;

final class FindShopQuery implements Query
{
    public function __construct(
        public readonly string $name,
        public readonly string $rate,
        public readonly string $store
    ) {
    }
}
