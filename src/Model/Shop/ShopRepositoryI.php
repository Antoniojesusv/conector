<?php

namespace App\Model\Shop;

use App\Model\Shop\ShopEntity;

interface ShopRepositoryI
{
    public function save(ShopEntity $connection): void;
    public function get(): ShopEntity;
}
