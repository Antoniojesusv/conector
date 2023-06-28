<?php

declare(strict_types=1);
namespace App\Shop\Domain;

final class Store
{
    public function __construct(
        private StoreId $storeId,
        private StoreType $storeType = StoreType::all
    ) {
    }

    public function id(): StoreId
    {
        return $this->storeId;
    }

    public function value(): string
    {
        return $this->storeType->value;
    }
}
