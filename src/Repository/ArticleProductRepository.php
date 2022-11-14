<?php

namespace App\Repository;

use App\DbConnectors\Factories\PdoFactoryI;
use App\DbConnectors\PdoConnector;
use App\Model\Synchronisation\ArticleProductEntity;
use Generator;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use PDO;

class ArticleProductRepository
{
    private PdoFactoryI $sqlPdoFactory;
    private PDO $connection;
    private PDO $mysqlConnection;
    private int $length = 0;
    private int $interval = 2000;
    private int $synchronisedArticles = 0;
    private float $currentPercentage = 0;
    private string $shopStore;
    const SHOPPER_GROUP_MAPPED_TO_RATE = [
        0 => 01,
        5 => 88
    ];

    public function __construct(
        PdoFactoryI $sqlPdoFactory,
        PdoFactoryI $mysqlPdoFactory,
        ContainerBagInterface $params
    ) {
        $this->sqlPdoFactory = $sqlPdoFactory;
        $this->mysqlPdoFactory = $mysqlPdoFactory;
        $this->params = $params;
        $this->from = 'articulo';
        $this->synchronisedArticles = $this->getArticleSynchronisedNumber();
        $this->shopStore = $this->params->get('shop.store');
        $sqlServerPdoConnector = $this->getPdoConnection();
        $sqlServerPdoConnector->hasConnection() ? $sqlServerPdoConnector->reconnect() : $sqlServerPdoConnector->connect();
        $this->connection = $sqlServerPdoConnector->getConnection();
        $mysqlPdoConnector = $this->mysqlPdoFactory->create();
        $mysqlPdoConnector->hasConnection() ? $mysqlPdoConnector->reconnect() : $mysqlPdoConnector->connect();
        $this->mysqlConnection = $mysqlPdoConnector->getConnection();
    }

    public function getPdoConnection(): PdoConnector
    {
        $authenticationMethod = $this->params->get("sql.authentication.method");

        if ($authenticationMethod === 'nm') {
            return $this->sqlPdoFactory->create('sqlServer');
        }

        return $this->sqlPdoFactory->create();
    }

    public function save(iterable $entityList): void
    {
        ini_set('max_execution_time', '3500');

        // $this->length = count($entityList);

        $this->clearJsonLogFile();

        $sqlStock = "UPDATE frthv_virtuemart_products ";
        $sqlStock .= "SET product_in_stock = :final, published = :productPublished ";
        $sqlStock .= "WHERE product_sku = :code";

        // $sqlPrice = "UPDATE frthv_virtuemart_product_prices ";
        // $sqlPrice .= "SET product_price = :price ";
        // $sqlPrice .= "WHERE virtuemart_product_id = :productId";

        // $sqlStock = "UPDATE bc_stock_available ";
        // $sqlStock .= "SET quantity = :final ";
        // $sqlStock .= "WHERE id_product = :productId;";

        // $sqlProductPrice = "UPDATE bc_product ";
        // $sqlProductPrice .= "SET price = :price, active = :productPublished ";
        // $sqlProductPrice .= "WHERE id_product = :productId;";

        // $sqlProductShopPrice = "UPDATE bc_product_shop ";
        // $sqlProductShopPrice .= "SET price = :price, active = :productPublished ";
        // $sqlProductShopPrice .= "WHERE id_product = :productId;";

        $queryStock = $this->mysqlConnection->prepare($sqlStock);
        // $queryPrice = $this->mysqlConnection->prepare($sqlPrice);
        // $queryPrice = $this->mysqlConnection->prepare($sqlProductPrice);
        // $queryShopPrice = $this->mysqlConnection->prepare($sqlProductShopPrice);

        $code = null;
        $final = null;
        $productPublished = null;
        // $pvp = null;
        // $productId = null;

        $queryStock->bindParam(":code", $code, PDO::PARAM_INT);
        $queryStock->bindParam(":final", $final, PDO::PARAM_STR);
        $queryStock->bindParam(":productPublished", $productPublished, PDO::PARAM_BOOL);
        // $queryStock->bindParam(":productId", $productId, PDO::PARAM_STR);
        // $queryPrice->bindParam(":price", $pvp, PDO::PARAM_STR);
        // $queryPrice->bindParam(":productPublished", $productPublished, PDO::PARAM_BOOL);
        // $queryPrice->bindParam(":productId", $productId, PDO::PARAM_STR);
        // $queryShopPrice->bindParam(":price", $pvp, PDO::PARAM_STR);
        // $queryShopPrice->bindParam(":productPublished", $productPublished, PDO::PARAM_BOOL);
        // $queryShopPrice->bindParam(":productId", $productId, PDO::PARAM_STR);

        $currentLength = 0;

        $maximumRange = $this->calculateMaximumRange();

        if ($maximumRange >= $this->length) {
            $this->interval = $this->calculateDifference();
        }

        foreach ($entityList as $key => $article) {
            if ($this->isWithinInterval($currentLength)) {
                // if ($currentLength === 2000) {
                //     break;
                // }
                // $currentKey = ($key + 1);

                // $this->currentPercentage = $this->calculateCurrentPertange($currentKey);

                $productId = $this->getProductId($article);
                $article->setProductId($productId);

                // $this->existRow($article);
            
                if ($productId !== '0') {
                    $code = $article->getCode();
                    $final = $article->getFinal();
                    $S01value = $this->getS01Value($article);
                    $article->setS01Value($S01value);
                    $productPublished = $this->isProductPublished($article);
                    $productId = $article->getProductId($article);
                    $shopperGroup = $this->getShopperGroupById($article);
                    $article->setShopperGroups($shopperGroup);
                    // $pvp = $article->getPvp();
                    $this->processShopperGroups($article);
                    $queryStock->execute();
                    $this->updateProductPrices($article);
                    // $queryPrice->execute();
                    // $queryShopPrice->execute();
                    $data = $article->toArray();
                    $data['published'] = $productPublished;
                    $data['updated'] = true;
                } else {
                    $data = $article->toArray();
                    $data['published'] = false;
                    $data['updated'] = false;
                    $code = $article->getCode();
                    $data['error'] = "The code $code does not exist";
                }
    
                $this->addToJsonLog($data);
            }

            if ($currentLength > $maximumRange) {
                break;
            }

            $currentLength++;
        }

        $this->synchronisedArticles = $this->synchronisedArticles + $this->interval;

        if ($maximumRange >= $this->length) {
            $this->resetSynchronisedArticles();
        }

        $this->saveSynchronisedArticles();
        $this->saveTotalArticles();
    }

    private function updateProductPrices(ArticleProductEntity $articleEntity): void
    {
        $sqlPrice = "UPDATE frthv_virtuemart_product_prices ";
        $sqlPrice .= "SET product_price = :price ";
        $sqlPrice .= "WHERE virtuemart_product_id = :productId ";
        $sqlPrice .= "AND virtuemart_shoppergroup_id = '0'";

        $rate = (int) $articleEntity->getRate();
        $shopperGroup = $this->getShopperGroupByRate($rate);
        $pvp = $articleEntity->getPvp();
        $productId = $articleEntity->getProductId();

        $queryPrice = $this->mysqlConnection->prepare($sqlPrice);

        if (!is_null($shopperGroup)) {
            $sqlPrice = "UPDATE frthv_virtuemart_product_prices ";
            $sqlPrice .= "SET product_price = :price ";
            $sqlPrice .= "WHERE virtuemart_product_id = :productId ";
            $sqlPrice .= "AND virtuemart_shoppergroup_id = :shopperGroup";

            $queryPrice = $this->mysqlConnection->prepare($sqlPrice);

            $queryPrice->bindParam(":shopperGroup", $shopperGroup, PDO::PARAM_INT);
        }

        $queryPrice->bindParam(":price", $pvp, PDO::PARAM_STR);
        $queryPrice->bindParam(":productId", $productId, PDO::PARAM_STR);

        $queryPrice->execute();
    }

    private function processShopperGroups(ArticleProductEntity $articleEntity): void
    {
        $rate = (int) $articleEntity->getRate();
        $shopperGroups = $articleEntity->getShopperGroups();

        $shopperGroup = $this->getShopperGroupByRate($rate);

        if (!$this->existShopperGroup($shopperGroup, $shopperGroups)) {
            $this->createShopperGroup($articleEntity, $shopperGroup);
        }
    }

    private function createShopperGroup(ArticleProductEntity $articleEntity, int $shopperGroup): void
    {
        $sql = "INSERT INTO bodecall2107.frthv_virtuemart_product_prices ";
        $sql .= "(virtuemart_product_id, virtuemart_shoppergroup_id, product_price, override, product_override_price, product_tax_id, product_discount_id, product_currency, product_price_publish_up, product_price_publish_down, price_quantity_start, price_quantity_end, created_on, created_by, modified_on, modified_by, locked_on, locked_by) ";
        $sql .= "VALUES(:productId, :shopperGroup, :productPrice, :override, :productOverridePrice, :productTaxId, :productDiscountId, :productCurrency, :defaultDatetime, :defaultDatetime, :priceQuantityStart, :priceQuantityEnd, NOW(), :createdBy, NOW(), :modifiedBy, NOW(), :lockedBy)";

        $row = $this->getProductPricesRow($articleEntity);

        $query = $this->mysqlConnection->prepare($sql);

        $productId = $articleEntity->getProductId();
        $productPrice = $articleEntity->getPvp();
        $override = !($row['override'] == '0');
        $productOverridePrice = $row['product_override_price'];
        $productTaxId = $row['product_tax_id'];
        $productDiscountId = $row['product_discount_id'];
        $productCurrency = $row['product_currency'];
        $defaultDatetime = '0000-00-00 00:00:00';
        $priceQuantityStart = $row['price_quantity_start'];
        $priceQuantityEnd = $row['price_quantity_end'];
        $createdBy = $row['created_by'];
        $modifiedBy = $row['modified_by'];
        $lockedBy = $row['locked_by'];

        $query->bindParam(":productId", $productId, PDO::PARAM_INT);
        $query->bindParam(":shopperGroup", $shopperGroup, PDO::PARAM_INT);
        $query->bindParam(":productPrice", $productPrice, PDO::PARAM_STR);
        $query->bindParam(":override", $override, PDO::PARAM_BOOL);
        $query->bindParam(":productOverridePrice", $productOverridePrice, PDO::PARAM_STR);
        $query->bindParam(":productTaxId", $productTaxId, PDO::PARAM_INT);
        $query->bindParam(":productDiscountId", $productDiscountId, PDO::PARAM_INT);
        $query->bindParam(":productCurrency", $productCurrency, PDO::PARAM_INT);
        $query->bindParam(":defaultDatetime", $defaultDatetime, PDO::PARAM_STR);
        $query->bindParam(":priceQuantityStart", $priceQuantityStart, PDO::PARAM_INT);
        $query->bindParam(":priceQuantityEnd", $priceQuantityEnd, PDO::PARAM_INT);
        $query->bindParam(":createdBy", $createdBy, PDO::PARAM_INT);
        $query->bindParam(":modifiedBy", $modifiedBy, PDO::PARAM_INT);
        $query->bindParam(":lockedBy", $lockedBy, PDO::PARAM_INT);

        $query->execute();
    }

    private function getProductPricesRow(ArticleProductEntity $articleEntity): array
    {
        $sql = "SELECT * FROM frthv_virtuemart_product_prices p WHERE virtuemart_product_id = :productId";

        $query = $this->mysqlConnection->prepare($sql);

        $productId = $articleEntity->getProductId();

        $query->bindValue(":productId", $productId, PDO::PARAM_STR);
        $query->execute();

        return $query->fetchAll()[0];
    }

    private function existShopperGroup(?int $shopperGroup, array $shopperGroups): bool
    {
        if (is_null($shopperGroup)) {
            return true;
        }

        return in_array($shopperGroup, $shopperGroups);
    }

    private function getShopperGroupByRate(int $rate): ?int
    {
        if (in_array($rate, $this::SHOPPER_GROUP_MAPPED_TO_RATE)) {
            return array_search($rate, $this::SHOPPER_GROUP_MAPPED_TO_RATE);
        }

        return null;
    }

    public function isProductPublished(ArticleProductEntity $articleEntity): bool
    {
        $low = $articleEntity->getLow();
        $stock = (int) $articleEntity->getFinal();
        $S01Value = $articleEntity->getS01Value();
        $internet = $articleEntity->getInternet();
        $pvp = (float) $articleEntity->getPvp();
        // $artCanon = $articleEntity->getArtCanon();

        if ($low === '1') {
            return false;
        }

        if ($stock <= 0) {
            return false;
        }

        if (!is_null($S01Value)) {
            if ($S01Value === 'F') {
                return false;
            }

            return true;
        }

        // if ($internet === '0') {
        //     return false;
        // }

        // if ($pvp <= 0) {
        //     return false;
        // }

        // if ($artCanon === '0') {
        //     return false;
        // }

        return true;
    }

    public function getAllByRateStockStoreAndCompany(
        string $rate,
        string $store,
        string $company
    ): Generator {
        if ($store === 'All') {
            $sql = "SELECT articulo.codigo, articulo.nombre, articulo.imagen, ";
            $sql .= "articulo.baja, articulo.internet, articulo.art_canon, ";
            $sql .= "pvp.pvp, pvp.tarifa, ";
            $sql .= "(SELECT SUM(FINAL) TOTALSTOCK FROM stocks2 WHERE articulo = articulo.codigo GROUP BY ARTICULO) AS final ";
            $sql .= "FROM " . $this->from . " ";
            $sql .= "INNER JOIN pvp ON (articulo.codigo = pvp.articulo) ";
            $sql .= "INNER JOIN stocks2 ON (articulo.codigo = stocks2.articulo) ";
            $sql .= "WHERE pvp.tarifa = :rate ";
            $sql .= "AND stocks2.empresa = :company";
        } else {
            $sql = "SELECT articulo.codigo, articulo.nombre, articulo.imagen, ";
            $sql .= "articulo.baja, articulo.internet, articulo.art_canon, ";
            $sql .= "pvp.pvp, pvp.tarifa, stocks2.final ";
            $sql .= "FROM " . $this->from . " ";
            $sql .= "INNER JOIN pvp ON (articulo.codigo = pvp.articulo) ";
            $sql .= "INNER JOIN stocks2 ON (articulo.codigo = stocks2.articulo) ";
            $sql .= "WHERE pvp.tarifa = :rate ";
            $sql .= "AND stocks2.almacen = :store ";
            $sql .= "AND stocks2.empresa = :company";
        }

        $query = $this->connection->prepare($sql);

        $query->bindParam(":rate", $rate, PDO::PARAM_STR);

        if ($store !== 'All') {
            $query->bindParam(":store", $store, PDO::PARAM_STR);
        }

        $query->bindParam(":company", $company, PDO::PARAM_STR);

        $query->execute();

        $articles = $query->fetchAll(PDO::FETCH_ASSOC);

        $this->length = $this->getArticlesLength($articles);

        if ($store === 'All') {
            $articles = $this->deleteRepeatedArticles($articles);
        }

        $articlesEntityList = $this->mapToEntity($articles);
        return $articlesEntityList;
    }

    private function getArticlesLengthToShow(
        string $rate,
        string $store,
        string $company
    ): int {
        if ($store === 'All') {
            $sql = "SELECT articulo.codigo, articulo.nombre, articulo.imagen, ";
            $sql .= "articulo.baja, articulo.internet, articulo.art_canon, ";
            $sql .= "pvp.pvp, pvp.tarifa, ";
            $sql .= "(SELECT SUM(FINAL) TOTALSTOCK FROM stocks2 WHERE articulo = articulo.codigo GROUP BY ARTICULO) AS final ";
            $sql .= "FROM " . $this->from . " ";
            $sql .= "INNER JOIN pvp ON (articulo.codigo = pvp.articulo) ";
            $sql .= "INNER JOIN stocks2 ON (articulo.codigo = stocks2.articulo) ";
            $sql .= "WHERE pvp.tarifa = :rate ";
            $sql .= "AND stocks2.empresa = :company";
        } else {
            $sql = "SELECT articulo.codigo, articulo.nombre, articulo.imagen, ";
            $sql .= "articulo.baja, articulo.internet, articulo.art_canon, ";
            $sql .= "pvp.pvp, pvp.tarifa, stocks2.final ";
            $sql .= "FROM " . $this->from . " ";
            $sql .= "INNER JOIN pvp ON (articulo.codigo = pvp.articulo) ";
            $sql .= "INNER JOIN stocks2 ON (articulo.codigo = stocks2.articulo) ";
            $sql .= "WHERE pvp.tarifa = :rate ";
            $sql .= "AND stocks2.almacen = :store ";
            $sql .= "AND stocks2.empresa = :company";
        }

        $query = $this->connection->prepare($sql);

        $query->bindParam(":rate", $rate, PDO::PARAM_STR);

        if ($store !== 'All') {
            $query->bindParam(":store", $store, PDO::PARAM_STR);
        }

        $query->bindParam(":company", $company, PDO::PARAM_STR);

        $query->execute();

        $articles = $query->fetchAll(PDO::FETCH_ASSOC);

        if ($store === 'All') {
            $articles = $this->deleteRepeatedArticles($articles);
            return $this->getArticlesLengthGivenaGenerator($articles);
        }

        return $this->getArticlesLength($articles);

        // return $this->getArticlesLengthGivenaGenerator($articles);
    }

    private function resetSynchronisedArticles(): void
    {
        $this->synchronisedArticles = 0;
    }

    private function getArticleSynchronisedNumber(): int
    {
        return intval($this->params->get("articles.synchronised"));
    }

    private function isWithinInterval(int $key): bool
    {
        return $key >= $this->synchronisedArticles && $key <= ($this->synchronisedArticles + $this->interval);
    }

    private function calculateMaximumRange(): int
    {
        return $this->synchronisedArticles + $this->interval;
    }

    private function calculateDifference(): int
    {
        return $this->length - $this->synchronisedArticles;
    }

    private function getArticlesLength(array $articles): int
    {
        $repeatedArticles = [];

        $currentLength = 0;

        foreach ($articles as $article) {
            $code = $article['codigo'];
            if (!in_array($code, $repeatedArticles)) {
                $repeatedArticles[] = $code;
                $currentLength += 1;
            }
        }

        return $currentLength;
    }

    private function getArticlesLengthGivenaGenerator(Generator $articles): int
    {
        $repeatedArticles = [];

        $currentLength = 0;

        foreach ($articles as $article) {
            $code = $article['codigo'];
            if (!in_array($code, $repeatedArticles)) {
                $repeatedArticles[] = $code;
                $currentLength += 1;
            }
        }

        return $currentLength;
    }

    private function deleteRepeatedArticles(array $articles): Generator
    {
        $repeatedArticles = [];

        foreach ($articles as $article) {
            $code = $article['codigo'];
            if (!in_array($code, $repeatedArticles)) {
                $repeatedArticles[] = $code;
                yield $article;
            }
        }
    }

    private function mapToEntity(iterable $articles): Generator
    {
        foreach ($articles as $article) {
            yield new ArticleProductEntity(
                $article['codigo'],
                $article['nombre'],
                $article['imagen'],
                $article['baja'],
                $article['internet'],
                $article['art_canon'],
                $article['pvp'],
                $article['tarifa'],
                $article['final']
            );
        }
    }

    // private function deleteRepeatedArticles(array $articles): array
    // {
    //     $repeatedArticles = [];
    //     $uniqueArticles = [];

    //     foreach ($articles as $article) {
    //         $code = $article['codigo'];
    //         if (!in_array($code, $repeatedArticles)) {
    //             $repeatedArticles[] = $code;
    //             $uniqueArticles[] = $article;
    //         }
    //     }

    //     return $uniqueArticles;
    // }

    // private function mapToEntity(array $articles): array
    // {
    //     return array_map(function ($article) {
    //         return new ArticleProductEntity(
    //             $article['codigo'],
    //             $article['nombre'],
    //             $article['imagen'],
    //             $article['baja'],
    //             $article['internet'],
    //             $article['art_canon'],
    //             $article['pvp'],
    //             $article['tarifa'],
    //             $article['final']
    //         );
    //     }, $articles);
    // }

    // private function calculateCurrentPertange(int $currentKey): float
    // {
    //     return ($currentKey * 100) / $this->length;
    // }

    public function getCurrentPercentage(): float
    {
        return $this->currentPercentage;
    }

    public function setCurrentPercentage(float $currentPercentage): void
    {
        $this->currentPercentage = $currentPercentage;
    }

    public function getAll(): array
    {
        $imageLogFile = $this->params->get("articles.log.file");
        $imageLog = file_get_contents($imageLogFile);
        return json_decode($imageLog, true);
    }

    private function addToJsonLog(array $data): void
    {
        $imageLogFile = $this->params->get("articles.log.file");
        $imageLog = file_get_contents($imageLogFile);
        $tempArray = json_decode($imageLog, true);
        array_push($tempArray, $data);
        $jsonData = json_encode($tempArray, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        file_put_contents($imageLogFile, $jsonData);
    }

    private function clearJsonLogFile(): void
    {
        $imageLogFile = $this->params->get("articles.log.file");
        $tempArray = [];
        $jsonData = json_encode($tempArray, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        file_put_contents($imageLogFile, $jsonData);
    }

    private function getProductId(ArticleProductEntity $articleEntity): string
    {
        $sql = "SELECT virtuemart_product_id FROM frthv_virtuemart_products ";
        $sql .= "WHERE product_sku = :code";

        // $sql = "SELECT id_product FROM admin_copia_dev_bdc.bc_product WHERE reference = :code";

        $query = $this->mysqlConnection->prepare($sql);

        $code = $articleEntity->getCode();

        $query->bindValue(":code", $code, PDO::PARAM_STR);

        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_COLUMN, 0);

        if (empty($result)) {
            return '0';
        }

        return $result[0];
    }

    private function getShopperGroupById(ArticleProductEntity $articleEntity): ?array
    {
        $sql = "SELECT p.virtuemart_shoppergroup_id FROM frthv_virtuemart_product_prices p ";
        $sql .= "WHERE virtuemart_product_id = :productId";

        $query = $this->mysqlConnection->prepare($sql);

        $code = $articleEntity->getProductId();

        $query->bindValue(":productId", $code, PDO::PARAM_STR);

        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_COLUMN, 0);

        if (empty($result)) {
            return [];
        }

        return $result;
    }
    
    private function getS01Value(ArticleProductEntity $articleEntity): ?string
    {
        $sql = "SELECT VALOR FROM multicam ";
        $sql .= "WHERE CODIGO = :code ";
        $sql .= "AND CAMPO = :campo";

        $query = $this->connection->prepare($sql);

        $code = $articleEntity->getCode();
        $campo = 'S01';

        $query->bindParam(":code", $code, PDO::PARAM_STR);
        $query->bindParam(":campo", $campo, PDO::PARAM_STR);

        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_COLUMN, 0);

        if (empty($result)) {
            return null;
        }

        preg_match('/[TF]/', $result[0], $matches);

        if (empty($matches)) {
            return null;
        }

        $S01Value = $matches[0];

        return $S01Value;
    }

    public function saveSynchronisedArticles(int $value = null): void
    {
        $envFilePath = $this->params->get('env.file');
        $envFile = file_get_contents($envFilePath);

        $synchronisedArticles = $value;

        if (is_null($value)) {
            $synchronisedArticles = $this->synchronisedArticles;
        }

        $envFileReplaced = preg_replace('/ARTICLES_SYNCHRONISED=.*/', "ARTICLES_SYNCHRONISED=\"$synchronisedArticles\"", $envFile);

        file_put_contents($envFilePath, $envFileReplaced);
    }

    public function saveTotalArticles(int $value = null): void
    {
        $envFilePath = $this->params->get('env.file');
        $envFile = file_get_contents($envFilePath);

        $totalArticles = $value;

        if (is_null($value)) {
            $totalArticles = $this->length;
        }

        $envFileReplaced = preg_replace('/TOTAL_ARTICLES=.*/', "TOTAL_ARTICLES=\"$totalArticles\"", $envFile);

        file_put_contents($envFilePath, $envFileReplaced);
    }

    public function getTotalArticles(): int
    {
        $rate = $this->params->get('shop.rate');
        $store = $this->params->get('shop.store');
        $company = '01';

        // $articlesGenerator = $this->getAllByRateStockStoreAndCompany($rate, $store, $company);

        return $this->getArticlesLengthToShow($rate, $store, $company);

        // $envFilePath = $this->params->get('env.file');
        // $envFile = file_get_contents($envFilePath);

        // preg_match('/TOTAL_ARTICLES="(.+)"/', $envFile, $matches);

        // return $matches[1];
    }

    public function getSynchronisationArticles(): string
    {
        $envFilePath = $this->params->get('env.file');
        $envFile = file_get_contents($envFilePath);

        preg_match('/ARTICLES_SYNCHRONISED="(.+)"/', $envFile, $matches);

        return $matches[1];
    }

    // private function

    // private function existRow(ArticleProductEntity $articleEntity): bool
    // {
    //     $sql = "SELECT EXISTS(SELECT * FROM frthv_virtuemart_products ";
    //     $sql .= "WHERE product_sku = :code )";

    //     $query = $this->mysqlConnection->prepare($sql);

    //     $code = $articleEntity->getCode();

    //     $query->bindValue(":code", $code, PDO::PARAM_INT);

    //     $query->execute();
    //     $result = $query->fetchAll(PDO::FETCH_COLUMN, 0);

    //     return !empty($result[0]);
    // }
}
