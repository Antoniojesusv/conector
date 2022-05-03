<?php

namespace App\Repository;

use App\DbConnectors\Factories\PdoFactoryI;
use App\DbConnectors\PdoConnector;
use App\Model\Stock2\Stocks2Entity;
use App\Model\Stock2\Stocks2RepositoryI;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use PDO;

class Stocks2Repository implements Stocks2RepositoryI
{
    private PdoFactoryI $sqlPdoFactory;
    private PDO $connection;

    public function __construct(
        PdoFactoryI $sqlPdoFactory,
        ContainerBagInterface $params
    ) {
        $this->sqlPdoFactory = $sqlPdoFactory;
        $this->params = $params;
        $this->from = 'stocks2';
        $sqlServerPdoConnector = $this->getPdoConnection();
        $sqlServerPdoConnector->hasConnection() ? $sqlServerPdoConnector->reconnect() : $sqlServerPdoConnector->connect();
        $this->connection = $sqlServerPdoConnector->getConnection();
    }

    public function getPdoConnection(): PdoConnector
    {
        $authenticationMethod = $this->params->get("sql.authentication.method");

        if ($authenticationMethod === 'nm') {
            return $this->sqlPdoFactory->create('sqlServer');
        }

        return $this->sqlPdoFactory->create();
    }

    private function mapToEntity(array $stores): Stocks2Entity
    {
        $storeFormated = [];

        $storeFormated['All'] = 'All';

        foreach ($stores as $store) {
            $value = $store['almacen'];
            $storeFormated[$value] = $value;
        }

        return new Stocks2Entity($storeFormated);
    }

    public function getStore(): Stocks2Entity
    {
        $sql = "SELECT s.almacen FROM " . $this->from . " s ";
        $sql .= "GROUP BY almacen ";
        $sql .= "ORDER BY almacen";

        $query = $this->connection->prepare($sql);

        $query->execute();
        $stores = $query->fetchAll(PDO::FETCH_ASSOC);
        $stocks2Entity = $this->mapToEntity($stores);
        return $stocks2Entity;
    }
}
