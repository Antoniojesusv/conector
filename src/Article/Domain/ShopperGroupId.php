<?php

namespace App\Article\Domain;

final class ShopperGroupId
{
    const SHOPPER_GROUP_MAPPED_TO_RATE = [
        '0' => '01',
        '5' => '88'
    ];
    private readonly string $id;

    public function __construct(
        private string $rate
    ) {
        $this->setId($rate);
    }

    public function value(): string
    {
        return $this->id;
    }

    public function isEquals(ShopperGroupId $other): bool
    {
        return $this->id === $other->value();
    }

    private function setId(string $rate): void
    {
        if (!in_array($rate, $this::SHOPPER_GROUP_MAPPED_TO_RATE)) {
            throw new TheRateNotExist("La tarifa {$rate} no existe");
        }

        $this->id = array_search($rate, $this::SHOPPER_GROUP_MAPPED_TO_RATE);
    }

    public function __toString(): string
    {
        return $this->value();
    }
}