<?php
namespace App\Article\Infrastructure\Persistance\Pdo;

use App\Article\Domain\Article;
use App\Article\Domain\Product;
use App\Article\Infrastructure\DataTransformer\ProductRepositoryDataTransformer;
use App\Shared\Infrastructure\Pdo\Dbal\Contract\PdoManager;

class BodecallArticleRepository
{
    private \PDO $connection;

    public function __construct(
        private PdoManager $mysqlPdoManager,
        private ProductRepositoryDataTransformer $productDataTransformer
    ) {
        $this->mysqlPdoManager = $mysqlPdoManager;
        $this->connection = $this->mysqlPdoManager->getConnection();
        $this->productDataTransformer = $productDataTransformer;
    }


    public function getProductId(Article $article): Product
    {
        $sql = "SELECT virtuemart_product_id FROM frthv_virtuemart_products ";
        $sql .= "WHERE product_sku = :code";

        $query = $this->connection->prepare($sql);

        /** @var \App\Article\Domain\Article */
        $code = $article->id();

        $query->bindValue(":code", $code, \PDO::PARAM_STR);

        $query->execute();
        $result = $query->fetchAll(\PDO::FETCH_COLUMN, 0);

        return $this->productDataTransformer->transform($article, $result);
    }

    public function getProductPricesRow(Product $product): array
    {
        $sql = "SELECT * FROM frthv_virtuemart_product_prices p WHERE virtuemart_product_id = :productId";

        $query = $this->connection->prepare($sql);

        $productId = $product->id();

        $query->bindValue(":productId", $productId, \PDO::PARAM_STR);
        $query->execute();

        return $query->fetchAll()[0];
    }

    public function save(Article $article, Product $product): void
    {
        $sqlStock = "UPDATE frthv_virtuemart_products ";
        $sqlStock .= "SET product_in_stock = :final ";
        $sqlStock .= "WHERE virtuemart_product_id = :code";

        $queryStock = $this->connection->prepare($sqlStock);

        $productId = $product->id();
        $stock = $article->stock();

        $queryStock->bindParam(":code", $productId, \PDO::PARAM_INT);
        $queryStock->bindParam(":final", $stock, \PDO::PARAM_STR);

        $queryStock->execute();
    }
}