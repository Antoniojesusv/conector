<?php

declare(strict_types=1);

namespace App\Shop\Application\Create;

use App\Repository\ShopRepository;
use App\Shared\Domain\Bus\Command\Contract\Command;
use App\Shared\Domain\Bus\Command\Contract\CommandHandler;
use App\Shop\Domain\Shop;
use App\Shop\Domain\ShopId;
use App\Shop\Domain\ShopName;
use App\Shop\Domain\ShopRate;
use App\Shop\Domain\Store;
use App\Shop\Domain\StoreId;

final class CreateShopCommandHandler implements CommandHandler
{
    public function __construct(
        private ShopRepository $shopRepository
    ) {
        $this->shopRepository = $shopRepository;
    }

    /**
     * Summary of __invoke
     * @param App\Shop\Application\Create\CreateShopCommand $command
     * @return mixed
     */
    public function __invoke(Command $command): void
    {
        $shopId = ShopId::generate();
        $shopName = new ShopName($command->name());
        $shopRate = new ShopRate($command->rate());
        $storeId = StoreId::generate();
        $store = new Store($storeId);
        $shop = new Shop($shopId, $shopName, $shopRate, $store);

        $storeEntity = new \App\Entity\Store();
        $shopEntity = new \App\Entity\Shop();
        $shopEntity->setName($shop->name());
        $shopEntity->setRate($shop->rate());
        $shopEntity->addStore($storeEntity);

        $this->shopRepository->save($shopEntity);
    }
}