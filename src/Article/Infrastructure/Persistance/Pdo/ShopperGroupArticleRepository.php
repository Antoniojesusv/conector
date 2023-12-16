<?php
namespace App\Article\Infrastructure\Persistance\Pdo;

use App\Article\Domain\Article;
use App\Article\Domain\Product;
use App\Article\Infrastructure\DataTransformer\ShopperGroupDataTransformer;
use App\Shared\Infrastructure\Pdo\Dbal\Contract\PdoManager;

class ShopperGroupArticleRepository
{
    private \PDO $connection;

    public function __construct(
        private PdoManager $mysqlPdoManager,
        private ShopperGroupDataTransformer $shopperGroupDataTransformer
    ) {
        $this->mysqlPdoManager = $mysqlPdoManager;
        $this->connection = $this->mysqlPdoManager->getConnection();
        $this->shopperGroupDataTransformer = $shopperGroupDataTransformer;
    }

    public function getShopperGroupById(Product $product): Product
    {
        $sql = "SELECT p.virtuemart_shoppergroup_id FROM frthv_virtuemart_product_prices p ";
        $sql .= "WHERE virtuemart_product_id = :productId";

        $query = $this->connection->prepare($sql);

        $code = $product->id();

        $query->bindValue(":productId", $code, \PDO::PARAM_STR);

        $query->execute();
        $result = $query->fetchAll(\PDO::FETCH_COLUMN, 0);

        return $this->shopperGroupDataTransformer->transform($product, $result);
    }

    public function createShopperGroup(Article $article, Product $product, array $row): void
    {
        $sql = "INSERT INTO frthv_virtuemart_product_prices ";
        $sql .= "(virtuemart_product_id, virtuemart_shoppergroup_id, product_price, override, product_override_price, product_tax_id, product_discount_id, product_currency, created_on, created_by, modified_on, modified_by, locked_by) ";
        $sql .= "VALUES(:productId, :shopperGroup, :productPrice, :override, :productOverridePrice, :productTaxId, :productDiscountId, :productCurrency, :createdOn, :createdBy, :modifiedOn, :modifiedBy, :lockedBy)";

        $query = $this->connection->prepare($sql);

        $productId = $product->id();
        $productPrice = $article->price();
        $override = !($row['override'] == '0');
        $productOverridePrice = $row['product_override_price'];
        $productTaxId = $row['product_tax_id'];
        $productDiscountId = $row['product_discount_id'];
        $productCurrency = $row['product_currency'];
        $createdBy = $row['created_by'];
        $modifiedBy = $row['modified_by'];
        $lockedBy = $row['locked_by'];

        $overrideInt = $this->fromBooltoInt($override);

        $parameters = [
            ':productId' => $productId,
            ':shopperGroup' => $product->shopperGroup()->id(),
            ':productPrice' => $productPrice,
            ':override' => $overrideInt,
            ':productOverridePrice' => $productOverridePrice,
            ':productTaxId' => $productTaxId,
            ':productDiscountId' => $productDiscountId,
            ':productCurrency' => $productCurrency,
            ':createdOn' => date('Y-m-d H:i:s'),
            ':createdBy' => $createdBy,
            ':modifiedOn' => date('Y-m-d H:i:s'),
            ':modifiedBy' => $modifiedBy,
            ':lockedBy' => $lockedBy
        ];

        $query->execute($parameters);
    }

    public function save(Article $article, Product $product): void
    {
        $sqlPriceShopperGroup = "UPDATE frthv_virtuemart_product_prices ";
        $sqlPriceShopperGroup .= "SET product_price = :price ";
        $sqlPriceShopperGroup .= "WHERE virtuemart_product_id = :productId ";
        $sqlPriceShopperGroup .= "AND virtuemart_shoppergroup_id = :shopperGroup";

        $queryPriceShopperGroup = $this->connection->prepare($sqlPriceShopperGroup);

        $productId = $product->id();
        $price = $article->price();
        $shopperGroup = $product->shopperGroup();
        $shopperGroupId = $shopperGroup->id();

        $queryPriceShopperGroup->bindParam(":productId", $productId, \PDO::PARAM_STR);
        $queryPriceShopperGroup->bindParam(":price", $price, \PDO::PARAM_STR);
        $queryPriceShopperGroup->bindParam(":shopperGroup", $shopperGroupId, \PDO::PARAM_INT);

        $queryPriceShopperGroup->execute();
    }

    private function fromBooltoInt(bool $bool): int
    {
        return $bool ? 1 : 0;
    }
}