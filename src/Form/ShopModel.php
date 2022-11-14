<?php

namespace App\Form;

use App\Model\Shop\ShopEntity;
use Symfony\Component\Validator\Constraints as Assert;

class ShopModel
{
    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    private string $name;
    
    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    private string $rate;

    private string $store;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name = ''): void
    {
        $this->name = $name;
    }

    public function getRate(): string
    {
        return $this->rate;
    }

    public function setRate(string $rate = '0'): void
    {
        $this->rate = $rate;
    }

    public function getStore(): string
    {
        return $this->store;
    }

    public function setStore(string $store = ''): void
    {
        $this->store = $store;
    }

    public function setData(ShopEntity $shop): void
    {
        $this->name = $shop->getName();
        $this->rate = $shop->getRate();
        $this->store = $shop->getStore();
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'rate' => $this->rate,
            'store' => $this->store
        ];
    }
}
