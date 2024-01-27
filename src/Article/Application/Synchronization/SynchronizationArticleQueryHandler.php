<?php

declare(strict_types=1);
namespace App\Article\Application\Synchronization;

use App\Article\Application\List\ArticleLogFactory;
use App\Article\Infrastructure\Persistance\Pdo\ArticleRepository;
use App\Article\Infrastructure\Persistance\Pdo\BodecallArticleRepository;
use App\Article\Infrastructure\Persistance\Pdo\EurowinArticleRepositoryNew;
use App\Article\Infrastructure\Persistance\Pdo\ShopperGroupArticleRepository;
use App\Shared\Domain\Bus\Query\Contract\Query;
use App\Shared\Domain\Bus\Query\Contract\QueryHandler;
use App\Shared\Infrastructure\Dispatcher\EventDispatcher;

final class SynchronizationArticleQueryHandler implements QueryHandler
{
    public function __construct(
        private EurowinArticleRepositoryNew $eurowinArticleRepositoryNew,
        private BodecallArticleRepository $bodecallArticleRepository,
        private ShopperGroupArticleRepository $shopperGroupArticleRepository,
        private ArticleLogFactory $articleLogFactory,
        private ArticleRepository $articleRepository,
        private EventDispatcher $eventDispatcher
    ) {
        $this->eurowinArticleRepositoryNew = $eurowinArticleRepositoryNew;
        $this->bodecallArticleRepository = $bodecallArticleRepository;
        $this->shopperGroupArticleRepository = $shopperGroupArticleRepository;
        $this->articleLogFactory = $articleLogFactory;
        $this->articleRepository = $articleRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Summary of __invoke
     * @param \App\Shared\Domain\Bus\Query\Contract\Query $query
     * @return mixed
     */
    public function __invoke(Query $query): mixed
    {
        $rate = $query->rate();
        $store = $query->store();
        $company = $query->company();

        $articleGenerator = $this->eurowinArticleRepositoryNew->getAllByRateStockStoreAndCompany($rate, $store, $company);

        $this->articleRepository->clearJsonLogFile();

        foreach ($articleGenerator as $key => $article) {
            $product = $this->bodecallArticleRepository->getProductId($article);

            if (!$product->existProduct()) {
                //$this->eventDispatcher->dispatch('ArticleSynchronized', ['totalArticle' => $article->totalArticle()]);
                $totalArticle = $article->totalArticle();
                $articleLog = $this->articleLogFactory->create($article, $totalArticle);
                $this->articleRepository->save($articleLog);
                $percentage = $this->calculatePercentage($totalArticle, $key);

                yield ['percentage' => $percentage];
                continue;
            }

            // $product = $this->shopperGroupArticleRepository->getShopperGroupById($product);

            // if (!$product->existShopperGroup()) {
            //     $this->bodecallArticleRepository->save($article, $product);
            //     $row = $this->bodecallArticleRepository->getProductPricesRow($product);
            //     $this->shopperGroupArticleRepository->createShopperGroup($article, $product, $row);
            // } else {
            $this->bodecallArticleRepository->save($article, $product);
            //     $this->shopperGroupArticleRepository->save($article, $product);
            // }

            $totalArticle = $article->totalArticle();
            $articleLog = $this->articleLogFactory->create($article, $totalArticle, 'Si');
            $this->articleRepository->save($articleLog);
            $percentage = $this->calculatePercentage($totalArticle, $key);

            yield ['percentage' => $percentage];
            //$this->eventDispatcher->dispatch('ArticleSynchronized', ['totalArticle' => $article->totalArticle()]);
        }

        return null;
    }

    private function calculatePercentage(int $totalArticle, int $key): float
    {
        $portion = $key;
        return round(($portion / $totalArticle) * 100);
    }
}