<?php

declare(strict_types=1);
namespace App\Shop\Domain;

final class Store
{
    public function __construct(
        private StoreId $storeId,
    ) {
        $this->storeId = $storeId;
    }

    public function id(): StoreId
    {
        return $this->storeId;
    }
}
