<?php
namespace App\Synchronisation\Domain;

class StockNumber
{
    public function __construct(
        private int $final
    ) {
        $this->final = $final;
    }

    public function final(): int
    {
        return $this->final;
    }

    public function isEquals(StockNumber $stockNumber): bool
    {
        return $this->final === $stockNumber->final();
    }
}
