<?php

namespace App\Model\Stock2;

use Exception;

class Stocks2Entity
{
    private array $store;

    public function __construct(
        array $store
    ) {
        $this->setStore($store);
    }

    public function getStore(): array
    {
        return $this->store;
    }

    public function setStore(array $store): void
    {
        if (empty($store)) {
            throw new Exception('The store cannot be empty');
        }

        $this->store = $store;
    }
}
