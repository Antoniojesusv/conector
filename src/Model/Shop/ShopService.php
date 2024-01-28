<?php

namespace App\Model\Shop;

use App\Model\Shop\ShopEntity;
use App\Model\Shop\OldShopRepositoryI;

class ShopService
{
    private OldShopRepositoryI $shopRepository;

    public function __construct(
        OldShopRepositoryI $shopRepository
    ) {
        $this->shopRepository = $shopRepository;
    }

    public function persist(array $data): void
    {
        [
            'name' => $name,
            'rate' => $rate,
            'store' => $store
        ] = $data;

        $shopEntity = new ShopEntity($name, $rate, $store);

        $this->shopRepository->save($shopEntity);
    }
}
