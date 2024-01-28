<?php

namespace App\Repository;

use App\Model\Shop\ShopEntity;
use App\Model\Shop\OldShopRepositoryI;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class OldShopRepository implements OldShopRepositoryI
{
    const SQL_SERVER_PATTERNS = [
        '/SHOP_NAME=.*/',
        '/SHOP_RATE=.*/',
        '/SHOP_STORE=.*/'
    ];

    public function __construct(
        ContainerBagInterface $params
    ) {
        $this->params = $params;
    }

    public function save(ShopEntity $entity): void
    {
        $envFilePath = $this->params->get('env.file');
        $envFile = file_get_contents($envFilePath);

        $envFile = $this->replaceSqlServerEnviroment($envFile, $entity);

        file_put_contents($envFilePath, $envFile);
    }

    public function get(): ShopEntity
    {
        $shopParams = $this->getParams();

        [
            'name' => $name,
            'rate' => $rate,
            'store' => $store
        ] = $shopParams;

        $shopEntity = new ShopEntity($name, $rate, $store);
        return $shopEntity;
    }

    private function getParams(): array
    {
        $connectionParams = [];

        $connectionParams['name'] = $this->params->get('shop.name');
        $connectionParams['rate'] = $this->params->get('shop.rate');
        $connectionParams['store'] = $this->params->get('shop.store');

        return $connectionParams;
    }

    private function replaceSqlServerEnviroment(string $envFile, ShopEntity $shopEntity): string
    {
        $name = $shopEntity->getName();
        $rate = $shopEntity->getRate();
        $store = $shopEntity->getStore();

        $replacements = [
            "SHOP_NAME=\"$name\"",
            "SHOP_RATE=$rate",
            "SHOP_STORE=\"$store\""
        ];

        return preg_replace($this::SQL_SERVER_PATTERNS, $replacements, $envFile);
    }
}
