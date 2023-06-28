<?php
namespace App\Synchronisation\Domain;

class Price
{
    public function __construct(
        private readonly int $pvp
    ) {
        $this->pvp = $pvp;
    }

    public function pvp(): int
    {
        return $this->pvp;
    }

    public function isEquals(Price $price): bool
    {
        return $this->pvp === $price->pvp();
    }
}
