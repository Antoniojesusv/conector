<?php
namespace App\Synchronisation\Domain;

use App\Synchronisation\Domain\CodeId;
use App\Synchronisation\Domain\Deregister;
use App\Synchronisation\Domain\Price;
use App\Synchronisation\Domain\S01;
use App\Synchronisation\Domain\StockNumber;

final class Article
{
    private bool $publish;

    public function __construct(
        private CodeId $codeId,
        private Price $price,
        private StockNumber $stockNumber,
        private ShopperGroup $shopperGroup,
        private Deregister $deregister,
        private S01 $s01
    ) {
        $this->codeId = $codeId;
        $this->price = $price;
        $this->stockNumber = $stockNumber;
        $this->shopperGroup = $shopperGroup;
        $this->deregister = $deregister;
        $this->s01 = $s01;
        $this->processPublication();
    }

    public function id(): CodeId
    {
        return $this->codeId;
    }

    public function price(): Price
    {
        return $this->price;
    }

    public function stockNumber(): StockNumber
    {
        return $this->stockNumber;
    }

    public function shopperGroup(): ShopperGroup
    {
        return $this->shopperGroup;
    }

    public function deregister(): Deregister
    {
        return $this->deregister;
    }

    public function s01(): S01
    {
        return $this->s01;
    }

    public function publish(): bool
    {
        return $this->publish;
    }

    public function isEquals(Article $other): bool
    {
        return $this->codeId->id() === $other->id()->id();
    }

    private function processPublication(): void
    {
        if ($this->deregister->value()) {
            $this->publish = false;
        }

        if ($this->stockNumber()->final() <= 0) {
            $this->publish = false;
        }

        if ($this->s01->value() === 'F') {
            $this->publish = false;
        }

        $this->publish = true;
    }
}
