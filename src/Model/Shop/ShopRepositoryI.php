<?php

namespace App\Model\Shop;

use App\Model\Shop\ShopEntity;

interface OldShopRepositoryI
{
    public function save(ShopEntity $connection): void;
    public function get(): ShopEntity;
}
