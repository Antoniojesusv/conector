<?php

declare(strict_types=1);
namespace App\Shop\Application\Find;

use App\Shared\Domain\Bus\Query\Contract\Query;
use App\Shared\Domain\Bus\Query\Contract\QueryHandler;
use App\Shop\Domain\ShopId;

final class FindShopByQueryHandler implements QueryHandler
{
    public function __invoke(Query $findShopByQuery): mixed
    {
        $shopId = ShopId::fromString($findShopByQuery->uuid());

        // return $this->connectionRepository->getByType($findShopByQuery->id);
        return '';
    }
}