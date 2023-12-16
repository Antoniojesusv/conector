<?php

declare(strict_types=1);
namespace App\Shop\Application\Find;

use App\Shop\Application\Find\FindShopQuery;
use App\Shop\Domain\Name;
use App\Shop\Domain\Rate;
use App\Shop\Domain\StoreId;

final class FindShopQueryHandler
{
    public function __construct(
        private ShopFinder $articleService
    ) {
    }

    public function __invoke(FindShopQuery $findShopQuery): void
    {
        $name = new Name($findShopQuery->name);
        $rate = new Rate($findShopQuery->rate);
        $store = $findShopQuery->store;

        // sthis->ShopFinder();
    }
}