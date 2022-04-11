<?php

namespace App\Model\Database;

use App\DbConnectors\Factories\PdoFactoryI;
use App\Model\Database\Entities\ConnectionBaseEntity;
use App\Model\Database\Factories\ConnectionFactoryI;
use App\Model\Database\Repositories\NmConnectionRepositoryI;
use App\Model\Database\Repositories\UwConnectionRepositoryI;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class DatabaseService
{
    private PdoFactoryI $sqlPdoFactory;
    private PdoFactoryI $mysqlPdoFactory;
    private NmConnectionRepositoryI $nmConnectionRepository;
    private UwConnectionRepositoryI $uWconnectionRepository;
    private ConnectionFactoryI $nmConnectionFactory;
    private ConnectionFactoryI $uWConnectionFactory;

    public function __construct(
        PdoFactoryI $sqlPdoFactory,
        PdoFactoryI $mysqlPdoFactory,
        NmConnectionRepositoryI $nmConnectionRepository,
        UwConnectionRepositoryI $uWconnectionRepository,
        ConnectionFactoryI $nmConnectionFactory,
        ConnectionFactoryI $uWConnectionFactory,
        ContainerBagInterface $params
    ) {
        $this->sqlPdoFactory = $sqlPdoFactory;
        $this->mysqlPdoFactory = $mysqlPdoFactory;
        $this->nmConnectionRepository = $nmConnectionRepository;
        $this->uWconnectionRepository = $uWconnectionRepository;
        $this->nmConnectionFactory = $nmConnectionFactory;
        $this->uWConnectionFactory = $uWConnectionFactory;
        $this->params = $params;
    }

    public function listConnections(): array
    {
        $authenticationMethod = $this->params->get("sql.authentication.method");

        $nmMysqlConnectionEntity = $this->nmConnectionRepository->getByType('mysqlServer');
        $mysqlPdoConnector = $this->mysqlPdoFactory->create();
        $mysqlPdoConnector->hasConnection() ? $mysqlPdoConnector->reconnect() : $mysqlPdoConnector->connect();

        if ($mysqlPdoConnector->hasConnection()) {
            $this->toogleStatus($nmMysqlConnectionEntity);
        }

        $message = $mysqlPdoConnector->getMessage();
        $nmMysqlConnectionEntity->setMessage($message);

        if ($authenticationMethod === 'nm') {
            $nmSqlConnectionEntity = $this->nmConnectionRepository->getByType('sqlServer');
            $sqlServerPdoConnector = $this->sqlPdoFactory->create('sqlServer');
            $sqlServerPdoConnector->hasConnection() ? $sqlServerPdoConnector->reconnect() : $sqlServerPdoConnector->connect();

            if ($sqlServerPdoConnector->hasConnection()) {
                $this->toogleStatus($nmSqlConnectionEntity);
            }

            $message = $sqlServerPdoConnector->getMessage();
            $nmSqlConnectionEntity->setMessage($message);

            return [
                $nmSqlConnectionEntity,
                $nmMysqlConnectionEntity
            ];
        }

        $uwSqlConnectionEntity = $this->uWconnectionRepository->get();
        $uwSqlServerPdoConnector = $this->sqlPdoFactory->create();
        $uwSqlServerPdoConnector->hasConnection() ? $uwSqlServerPdoConnector->reconnect() : $uwSqlServerPdoConnector->connect();

        if ($uwSqlServerPdoConnector->hasConnection()) {
            $this->toogleStatus($uwSqlConnectionEntity);
        }

        $message = $uwSqlServerPdoConnector->getMessage();
        $uwSqlConnectionEntity->setMessage($message);

        return [
            $uwSqlConnectionEntity,
            $nmMysqlConnectionEntity
        ];
    }

    public function persist(array $data, string $authentication): void
    {
        $data['status'] = false;
        $data['message'] = '';

        if ($authentication === 'uwSqlServer') {
            $connection = $this->uWConnectionFactory->create($data);
            $this->uWconnectionRepository->save($connection);
        }

        if ($authentication === 'nmSqlServer') {
            $data['type'] = 'sqlServer';
            $connection = $this->nmConnectionFactory->create($data);
            $this->nmConnectionRepository->save($connection);
        }

        if ($authentication === 'mysqlServer') {
            $data['type'] = 'mysqlServer';
            $connection = $this->nmConnectionFactory->create($data);
            $this->nmConnectionRepository->save($connection);
        }
    }

    private function toogleStatus(ConnectionBaseEntity $connection): void
    {
        $status = !$connection->getStatus();
        $connection->setStatus($status);
    }
}
