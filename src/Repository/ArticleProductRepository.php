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
    private float $currentPercentage = 0;

    public function __construct(
        PdoFactoryI $sqlPdoFactory,
        PdoFactoryI $mysqlPdoFactory,
        ContainerBagInterface $params
    ) {
        $this->sqlPdoFactory = $sqlPdoFactory;
        $this->mysqlPdoFactory = $mysqlPdoFactory;
        $this->params = $params;
        $this->from = 'articulo';
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

        // $sqlStock = "UPDATE frthv_virtuemart_products ";
        // $sqlStock .= "SET product_in_stock = :final, published = :productPublished ";
        // $sqlStock .= "WHERE product_sku = :code";

        // $sqlPrice = "UPDATE frthv_virtuemart_product_prices ";
        // $sqlPrice .= "SET product_price = :price ";
        // $sqlPrice .= "WHERE virtuemart_product_id = :productId";

        $sqlStock = "UPDATE bc_stock_available ";
        $sqlStock .= "SET quantity = :final ";
        $sqlStock .= "WHERE id_product = :productId;";

        $sqlProductPrice = "UPDATE bc_product ";
        $sqlProductPrice .= "SET price = :price, active = :productPublished ";
        $sqlProductPrice .= "WHERE id_product = :productId;";

        $sqlProductShopPrice = "UPDATE bc_product_shop ";
        $sqlProductShopPrice .= "SET price = :price, active = :productPublished ";
        $sqlProductShopPrice .= "WHERE id_product = :productId;";

        $queryStock = $this->mysqlConnection->prepare($sqlStock);
        $queryPrice = $this->mysqlConnection->prepare($sqlProductPrice);
        $queryShopPrice = $this->mysqlConnection->prepare($sqlProductShopPrice);

        // $code = null;
        $final = null;
        $productPublished = null;
        $pvp = null;
        $productId = null;

        // $queryStock->bindParam(":code", $code, PDO::PARAM_INT);
        $queryStock->bindParam(":final", $final, PDO::PARAM_STR);
        $queryStock->bindParam(":productId", $productId, PDO::PARAM_STR);
        $queryPrice->bindParam(":price", $pvp, PDO::PARAM_STR);
        $queryPrice->bindParam(":productPublished", $productPublished, PDO::PARAM_BOOL);
        $queryPrice->bindParam(":productId", $productId, PDO::PARAM_STR);
        $queryShopPrice->bindParam(":price", $pvp, PDO::PARAM_STR);
        $queryShopPrice->bindParam(":productPublished", $productPublished, PDO::PARAM_BOOL);
        $queryShopPrice->bindParam(":productId", $productId, PDO::PARAM_STR);

        $currentLength = 0;

        foreach ($entityList as $key => $article) {
            if ($currentLength === 2000) {
                break;
            }
            // $currentKey = ($key + 1);

            // $this->currentPercentage = $this->calculateCurrentPertange($currentKey);

            $productId = $this->getProductId($article);

            // $this->existRow($article);
            
            if ($productId !== '0') {
                // $code = $article->getCode();
                $final = $article->getFinal();
                $productPublished = $this->isProductPublished($article);
                $productId = $this->getProductId($article);
                $pvp = $article->getPvp();
                $queryStock->execute();
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

            $currentLength++;
        }
    }

    public function isProductPublished(ArticleProductEntity $articleEntity): bool
    {
        $low = $articleEntity->getLow();
        $internet = $articleEntity->getInternet();
        $pvp = (float) $articleEntity->getPvp();
        // $artCanon = $articleEntity->getArtCanon();

        if ($low === '1') {
            return false;
        }

        if ($internet === '0') {
            return false;
        }

        if ($pvp <= 0) {
            return false;
        }

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
        // $sql = "SELECT virtuemart_product_id FROM frthv_virtuemart_products ";
        // $sql .= "WHERE product_sku = :code";

        $sql = "SELECT id_product FROM admin_copia_dev_bdc.bc_product WHERE reference = :code";

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
