<?php

namespace App\Article\Domain;

class Rate
{
    const RATE_NUMBERS = [
        '01',
        '02',
        '77',
        '82',
        '88'
    ];

    public function __construct(
        private readonly string $rate
    ) {
    }

    public function value(): string
    {
        return $this->rate;
    }

    public function isEquals(Rate $rateNumber): bool
    {
        return $this->rate === $rateNumber->value();
    }

    private function setRate(string $rate): void
    {
        if (!in_array($rate, $this::RATE_NUMBERS)) {
            throw new TheRateNotExist("La tarifa {$rate} no existe");
        }

        $this->rate = $rate;
    }

    public function __toString(): string
    {
        return $this->value();
    }
}