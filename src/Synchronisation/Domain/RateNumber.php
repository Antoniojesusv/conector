<?php
namespace App\Synchronisation\Domain;

use App\Synchronisation\Domain\Exception\TheRateNotExist;

class RateNumber
{
    const RATENUMBERS = [
        '01',
        '02',
        '77',
        '82',
        '88'
    ];

    public function __construct(
        private readonly string $rate
    ) {
        $this->rate = $rate;
    }

    public function rate(): string
    {
        return $this->rate;
    }

    public function isEquals(RateNumber $rateNumber): bool
    {
        return $this->rate === $rateNumber->rate();
    }

    private function setRate(string $rate): void
    {
        if (!in_array($rate, $this::RATENUMBERS)) {
            throw new TheRateNotExist("La tarifa {$rate} no existe");
        }

        $this->rate = $rate;
    }
}
