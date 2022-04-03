<?php

namespace App\Model\Database\Services;

use App\DbConnectors\MysqlPdoConnector;
use App\DbConnectors\SqlServerPdoConnector;
use App\Model\Common\Repository;
use App\Model\Database\Entities\ConnectionEntity;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class DatabaseService
{
    private SqlServerPdoConnector $sqlPdoConnector;
    private MysqlPdoConnector $mysqlPdoConnector;
    private Repository $connectionRepository;
    private ?ConnectionEntity $sqlCe = null;
    private ?ConnectionEntity $mysqlCe = null;
    private array $ceList = [];

    public function __construct(
        SqlServerPdoConnector $sqlPdoConnector,
        MysqlPdoConnector $mysqlPdoConnector,
        Repository $connectionRepository,
        ContainerBagInterface $params
    ) {
        $this->sqlPdoConnector = $sqlPdoConnector;
        $this->mysqlPdoConnector = $mysqlPdoConnector;
        $this->connectionRepository = $connectionRepository;
        $this->params = $params;
    }

    public function getConnections(): array
    {
        $this->sqlPdoConnector->connect();
        $this->mysqlPdoConnector->connect();
        $this->buildConnectionEntity();
        return $this->ceList;
    }

    public function getSqlCe(): ConnectionEntity
    {
        $this->buildSqlCe();
        $sqlServerPassword = $this->params->get('sql.password');
        $this->sqlCe->setPassword($sqlServerPassword);
        return $this->sqlCe;
    }

    public function getMysqlCe(): ConnectionEntity
    {
        $this->buildMysqlCe();
        $mysqlPassword = $this->params->get('mysql.password');
        $this->mysqlCe->setPassword($mysqlPassword);
        return $this->mysqlCe;
    }

    private function buildConnectionEntity(): void
    {
        $this->buildSqlCe();
        $this->buildMysqlCe();
        $this->ceList[0] = $this->sqlCe;
        $this->ceList[1] = $this->mysqlCe;
    }

    private function buildSqlCe(): void
    {
        $sqlServerUser = $this->params->get('sql.user');
        $sqlServerAddress = $this->params->get('sql.address');
        $sqlServerDatabase = $this->params->get('sql.database');
        $sqlServerExposedPort = $this->params->get('sql.exposed.port');
        $sqlServerStatus = $this->sqlPdoConnector->hasConnection();
        $message = '';

        if (!$sqlServerStatus) {
            $message = $this->sqlPdoConnector->getMessage();
        }

        $sqlCe = new ConnectionEntity(
            $sqlServerUser,
            null,
            $sqlServerAddress,
            $sqlServerDatabase,
            $sqlServerExposedPort,
            'sqlServer',
            $sqlServerStatus,
            $message
        );

        $this->sqlCe = $sqlCe;
    }

    private function buildMysqlCe(): void
    {
        $mysqlUser = $this->params->get('mysql.user');
        $mysqlAddress = $this->params->get('mysql.address');
        $mysqlDatabase = $this->params->get('mysql.database');
        $mysqlExposedPort = $this->params->get('mysql.exposed.port');
        $mysqlStatus = $this->mysqlPdoConnector->hasConnection();
        $message = '';

        if (!$mysqlStatus) {
            $message = $this->mysqlPdoConnector->getMessage();
        }

        $mysqlCe = new ConnectionEntity(
            $mysqlUser,
            null,
            $mysqlAddress,
            $mysqlDatabase,
            $mysqlExposedPort,
            'mysqlServer',
            $mysqlStatus,
            $message
        );

        $this->mysqlCe = $mysqlCe;
    }

    public function persist(ConnectionEntity $connectionEntity): void
    {
        $this->connectionRepository->save($connectionEntity);
    }

    public function reconnect(): void
    {
        $this->sqlPdoConnector->reconnect();
    }
}
