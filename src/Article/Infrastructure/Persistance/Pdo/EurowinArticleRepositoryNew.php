<?php
namespace App\Article\Infrastructure\Persistance\Pdo;

use App\Article\Infrastructure\DataTransformer\ArticleRepositoryDataTransformer;
use App\Shared\Infrastructure\Pdo\Dbal\Contract\PdoManager;

class EurowinArticleRepositoryNew
{
    const FROM = 'articulo';
    private \PDO $connection;

    public function __construct(
        private PdoManager $sqlServerPdoManager,
        private ArticleRepositoryDataTransformer $articleDataTransformer
    ) {
        $this->sqlServerPdoManager = $sqlServerPdoManager;
        $this->connection = $this->sqlServerPdoManager->getConnection();
        $this->articleDataTransformer = $articleDataTransformer;
    }

    /**
     * Summary of getAllByRateStockStoreAndCompany
     * @param string $rate
     * @param string $store
     * @param string $company
     * @return \Generator<\App\Article\Domain\Article>
     */
    public function getAllByRateStockStoreAndCompany(
        string $rate,
        string $store,
        string $company
    ): \Generator {
        $sql = 'SELECT articulo.codigo, ';
        $sql .= 'stocks2.final, ';
        $sql .= 'pvp.PVP, ';
        $sql .= 'pvp.tarifa ';
        $sql .= 'FROM ' . self::FROM . ' ';
        $sql .= 'INNER JOIN pvp ON (articulo.codigo = pvp.articulo) ';
        $sql .= 'INNER JOIN stocks2 ON (articulo.codigo = stocks2.articulo) ';
        $sql .= 'WHERE pvp.tarifa = :rate ';
        $sql .= 'AND stocks2.almacen = :store ';
        $sql .= 'AND stocks2.empresa = :company';

        if ($store === 'All' && $rate === 'All') {
            $sql = 'SELECT articulo.codigo, ';
            $sql .= 'SUM(stocks2.final) AS TOTAL_STOCK, ';
            $sql .= 'pvp.PVP, ';
            $sql .= 'pvp.tarifa ';
            $sql .= 'FROM ' . self::FROM . ' ';
            $sql .= 'INNER JOIN pvp ON (articulo.codigo = pvp.articulo) ';
            $sql .= 'INNER JOIN stocks2 ON (articulo.codigo = stocks2.articulo) ';
            $sql .= "WHERE pvp.tarifa IN ('01','88') ";
            $sql .= 'AND stocks2.empresa = :company ';
            $sql .= 'GROUP BY articulo.codigo, pvp.PVP, pvp.tarifa';
        }

        if ($store !== 'All' && $rate === 'All') {
            $sql = 'SELECT articulo.codigo, ';
            $sql .= 'stocks2.final, ';
            $sql .= 'pvp.PVP, ';
            $sql .= 'pvp.tarifa ';
            $sql .= 'FROM ' . self::FROM . ' ';
            $sql .= 'INNER JOIN pvp ON (articulo.codigo = pvp.articulo) ';
            $sql .= 'INNER JOIN stocks2 ON (articulo.codigo = stocks2.articulo) ';
            $sql .= "WHERE pvp.tarifa IN ('01','88') ";
            $sql .= 'AND stocks2.almacen = :store ';
            $sql .= 'AND stocks2.empresa = :company ';
        }

        if ($store === 'All' && $rate !== 'All') {
            $sql = 'SELECT articulo.codigo, ';
            $sql .= 'SUM(stocks2.final) AS TOTAL_STOCK, ';
            $sql .= 'pvp.PVP, ';
            $sql .= 'pvp.tarifa ';
            $sql .= 'FROM ' . self::FROM . ' ';
            $sql .= 'INNER JOIN pvp ON (articulo.codigo = pvp.articulo) ';
            $sql .= 'INNER JOIN stocks2 ON (articulo.codigo = stocks2.articulo) ';
            $sql .= 'WHERE pvp.tarifa = :rate ';
            $sql .= 'AND stocks2.empresa = :company ';
            $sql .= 'GROUP BY CODIGO, pvp.PVP, pvp.tarifa';
        }

        $query = $this->connection->prepare($sql);

        if ($rate !== 'All') {
            $query->bindParam(':rate', $rate, \PDO::PARAM_STR);
        }

        if ($store !== 'All') {
            $query->bindParam(':store', $store, \PDO::PARAM_STR);
        }

        $query->bindParam(':company', $company, \PDO::PARAM_STR);

        $query->execute();

        $result = $query->fetchAll(\PDO::FETCH_ASSOC);

        $totalResult = count($result);
        $articles = $this->articleDataTransformer->transform($result, $totalResult);

        foreach ($articles as $article) {
            yield $article;
        }
    }
}