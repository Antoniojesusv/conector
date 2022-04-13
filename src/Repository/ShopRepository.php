<?php

namespace App\Repository;

use App\Model\Shop\ShopEntity;
use App\Model\Shop\ShopRepositoryI;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class ShopRepository implements ShopRepositoryI
{
    const SQL_SERVER_PATTERNS = [
        '/SHOP_NAME=.*/',
        '/SHOP_RATE=.*/'
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
            'rate' => $rate
        ] = $shopParams;

        $shopEntity = new ShopEntity($name, $rate);
        return $shopEntity;
    }

    private function getParams(): array
    {
        $connectionParams = [];
        
        $connectionParams['name'] = $this->params->get('shop.name');
        $connectionParams['rate'] = $this->params->get('shop.rate');

        return $connectionParams;
    }

    private function replaceSqlServerEnviroment(string $envFile, ShopEntity $shopEntity): string
    {
        $name = $shopEntity->getName();
        $rate = $shopEntity->getRate();

        $replacements = [
            "SHOP_NAME=\"$name\"",
            "SHOP_RATE=$rate"
        ];

        return preg_replace($this::SQL_SERVER_PATTERNS, $replacements, $envFile);
    }
}
