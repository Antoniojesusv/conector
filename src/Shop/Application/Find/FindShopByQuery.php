<?php

declare(strict_types=1);
namespace App\Shop\Application\Find;

use App\Shared\Domain\Bus\Query\Contract\Query;

final class FindShopByQuery extends Query
{
    public function __construct(
        public readonly string $uuid,
    ) {
        parent::__construct();
    }

    public function uuid(): string
    {
        return $this->uuid;
    }
}
