<?php
namespace App\Article\Infrastructure\Persistance\Pdo;

use App\DbConnectors\Factories\PdoFactoryI;
use App\DbConnectors\PdoConnector;
use App\Article\Domain\SourceArticleRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use PDO;

class EurowinArticleRepository implements SourceArticleRepository
{
    private PDO $connection;
    private string $from;

    public function __construct(
        private PdoFactoryI $sqlPdoFactory,
        private ContainerBagInterface $params
    ) {
        $this->sqlPdoFactory = $sqlPdoFactory;
        $this->params = $params;
        $this->from = 'articulo';
        $sqlServerPdoConnector = $this->getPdoConnection();
        $sqlServerPdoConnector->hasConnection() ? $sqlServerPdoConnector->reconnect() : $sqlServerPdoConnector->connect();
        $this->connection = $sqlServerPdoConnector->getConnection();
    }

    public function getPdoConnection(): PdoConnector
    {
        $authenticationMethod = $this->params->get('sql.authentication.method');

        if ($authenticationMethod === 'nm') {
            return $this->sqlPdoFactory->create('sqlServer');
        }

        return $this->sqlPdoFactory->create();
    }

    public function getAllByRateStockStoreAndCompany(
        string $rate,
        string $store,
        string $company
    ): array {
        $sql = 'SELECT articulo.codigo, articulo.nombre, articulo.imagen, ';
        $sql .= 'articulo.baja, articulo.internet, articulo.art_canon, ';
        $sql .= 'pvp.pvp, pvp.tarifa, stocks2.final, multicam.campo ';
        $sql .= 'FROM ' . $this->from . ' ';
        $sql .= 'INNER JOIN pvp ON (articulo.codigo = pvp.articulo) ';
        $sql .= 'INNER JOIN stocks2 ON (articulo.codigo = stocks2.articulo) ';
        $sql .= 'INNER JOIN multicam ON (articulo.codigo = multicam.codigo) ';
        $sql .= 'WHERE pvp.tarifa = :rate ';
        $sql .= 'AND stocks2.almacen = :store ';
        $sql .= 'AND stocks2.empresa = :company ';
        $sql .= 'AND multicam.campo = :field';

        if ($store === 'All' && $rate === 'All') {
            $sql = 'SELECT articulo.codigo, articulo.nombre, articulo.imagen, ';
            $sql .= 'articulo.baja, articulo.internet, articulo.art_canon, ';
            $sql .= 'pvp.pvp, pvp.tarifa, multicam.campo, ';
            $sql .= '(SELECT SUM(FINAL) TOTALSTOCK FROM stocks2 WHERE articulo = articulo.codigo GROUP BY ARTICULO) AS final ';
            $sql .= 'FROM ' . $this->from . ' ';
            $sql .= 'INNER JOIN pvp ON (articulo.codigo = pvp.articulo) ';
            $sql .= 'INNER JOIN stocks2 ON (articulo.codigo = stocks2.articulo) ';
            $sql .= 'INNER JOIN multicam ON (articulo.codigo = multicam.codigo) ';
            $sql .= "WHERE pvp.tarifa IN ('01','88') ";
            $sql .= 'AND stocks2.empresa = :company ';
            $sql .= 'AND multicam.campo = :field';
        }

        if ($store !== 'All' && $rate === 'All') {
            $sql = 'SELECT articulo.codigo, articulo.nombre, articulo.imagen, ';
            $sql .= 'articulo.baja, articulo.internet, articulo.art_canon, ';
            $sql .= 'pvp.pvp, pvp.tarifa, stocks2.final, multicam.campo ';
            $sql .= 'FROM ' . $this->from . ' ';
            $sql .= 'INNER JOIN pvp ON (articulo.codigo = pvp.articulo) ';
            $sql .= 'INNER JOIN stocks2 ON (articulo.codigo = stocks2.articulo) ';
            $sql .= 'INNER JOIN multicam ON (articulo.codigo = multicam.codigo) ';
            $sql .= "WHERE pvp.tarifa IN ('01','88') ";
            $sql .= 'AND stocks2.almacen = :store ';
            $sql .= 'AND stocks2.empresa = :company ';
            $sql .= 'AND multicam.campo = :field';
        }

        if ($store === 'All' && $rate !== 'All') {
            $sql = 'SELECT articulo.codigo, articulo.nombre, articulo.imagen, ';
            $sql .= 'articulo.baja, articulo.internet, articulo.art_canon, ';
            $sql .= 'pvp.pvp, pvp.tarifa, multicam.campo, ';
            $sql .= '(SELECT SUM(FINAL) TOTALSTOCK FROM stocks2 WHERE articulo = articulo.codigo GROUP BY ARTICULO) AS final ';
            $sql .= 'FROM ' . $this->from . ' ';
            $sql .= 'INNER JOIN pvp ON (articulo.codigo = pvp.articulo) ';
            $sql .= 'INNER JOIN stocks2 ON (articulo.codigo = stocks2.articulo) ';
            $sql .= 'INNER JOIN multicam ON (articulo.codigo = multicam.codigo) ';
            $sql .= 'WHERE pvp.tarifa = :rate ';
            $sql .= 'AND stocks2.empresa = :company ';
            $sql .= 'AND multicam.campo = :field';
        }

        $query = $this->connection->prepare($sql);

        if ($rate !== 'All') {
            $query->bindParam(':rate', $rate, PDO::PARAM_STR);
        }

        if ($store !== 'All') {
            $query->bindParam(':store', $store, PDO::PARAM_STR);
        }

        $s01 = 'S01';
        $query->bindParam(':company', $company, PDO::PARAM_STR);
        $query->bindParam(':field', $s01, PDO::PARAM_STR);

        $query->execute();

        $articles = $query->fetchAll(PDO::FETCH_ASSOC);

        return $articles;
    }
}