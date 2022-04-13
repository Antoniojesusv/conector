<?php

namespace App\Model\Shop;

use Exception;

class ShopEntity
{
    private string $name;
    private int $rate;

    public function __construct(
        string $name,
        string $rate
    ) {
        $this->setName($name);
        $this->setRate($rate);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        if (empty($name)) {
            throw new Exception('The name cannot be empty');
        }

        $this->name = $name;
    }

    public function getRate(): int
    {
        return $this->rate;
    }

    public function setRate(int $rate): void
    {
        $this->rate = $rate;
    }
}
