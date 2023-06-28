<?php
namespace App\Synchronisation\Infrastructure\Persistance\Pdo;

use App\Synchronisation\Domain\Article;
use App\Synchronisation\Domain\ShopperGroup;
use App\Synchronisation\Domain\SourceArticleRepository;
use App\Synchronisation\Domain\CodeId;
use App\Synchronisation\Domain\Deregister;
use App\Synchronisation\Domain\Price;
use App\Synchronisation\Domain\S01;
use App\Synchronisation\Domain\ShopperGroupId;
use App\Synchronisation\Domain\StockNumber;

class EurowinArticleMappingRepository implements SourceArticleRepository
{
    public function __construct(
        private SourceArticleRepository $eurowinArticleRepository
    ) {
        $this->eurowinArticleRepository = $eurowinArticleRepository;
    }

    public function getAllByRateStockStoreAndCompany(
        string $rate,
        string $store,
        string $company
    ): array {
        $articlesRows = $this->eurowinArticleRepository->getAllByRateStockStoreAndCompany($rate, $store, $company);
        return array_map(fn ($article) => $this->buildArticle($article), $articlesRows);
    }

    private function buildArticle($article): Article
    {
        $codeId = new CodeId($article['codigo']);
        $price = new Price($article['pvp']);
        $stock = new StockNumber($article['final']);
        $shopperGroupId = new ShopperGroupId($article['tarifa']);
        $shopperGroup = new ShopperGroup($shopperGroupId);
        $deregister = new Deregister($article['baja']);
        $s01 = new S01($article['campo']);

        return new Article(
            $codeId,
            $price,
            $stock,
            $shopperGroup,
            $deregister,
            $s01
        );
    }
}
