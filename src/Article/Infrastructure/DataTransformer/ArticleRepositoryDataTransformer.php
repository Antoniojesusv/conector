<?php

declare(strict_types=1);
namespace App\Article\Infrastructure\DataTransformer;

use App\Article\Application\Synchronization\FactoryMethod\ArticleFactory;

class ArticleRepositoryDataTransformer
{
    public function __construct(private ArticleFactory $articleFactory)
    {
        $this->articleFactory = $articleFactory;
    }
    public function transform(array $data, int $totalResult): array
    {
        return array_map(function (array $row) use ($totalResult) {

            ['codigo' => $articleId, 'PVP' => $price, 'tarifa' => $rate] = $row;

            if (array_key_exists("TOTAL_STOCK", $row)) {
                ['TOTAL_STOCK' => $stock] = $row;
            } else {
                ['final' => $stock] = $row;
            }

            return $this->articleFactory->create($articleId, $stock, $price, $rate, $totalResult);
        }, $data);
    }
}