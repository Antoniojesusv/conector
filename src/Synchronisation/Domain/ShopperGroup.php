<?php
namespace App\Synchronisation\Domain;

use App\Synchronisation\Domain\ShopperGroupId;

class ShopperGroup
{
    public function __construct(
        private ShopperGroupId $shopperGroupId
    ) {
        $this->shopperGroupId = $shopperGroupId;
    }

    public function id(): ShopperGroupId
    {
        return $this->shopperGroupId;
    }

    public function isEquals(ShopperGroup $shopperGroup): bool
    {
        return $this->shopperGroupId->id() === $shopperGroup->id()->id();
    }
}
