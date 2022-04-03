<?php

namespace App\Repository;

use App\Common\Repository;
use App\DbConnectors\MysqlPdoConnector;
use App\DbConnectors\SqlServerPdoConnector;
use App\Model\Database\Entities\ConnectionEntity;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class ConnectionRepository extends Repository
{
    const SQL_SERVER_PATTERNS = [
        '/SQL_SERVER_USER=.*/',
        '/SA_PASSWORD=.*/',
        '/SQL_SERVER_ADDRESS=.*/',
        '/SQL_SERVER_DATABASE=.*/',
        '/SQL_SERVER_EXPOSED_PORT=.*/',
    ];
    const MYSQL_SERVER_PATTERNS = [
        '/MYSQL_USER=.*/',
        '/MYSQL_PASSWORD=.*/',
        '/MYSQL_ADDRESS=.*/',
        '/MYSQL_DATABASE=.*/',
        '/MYSQL_EXPOSED_PORT=.*/',
    ];

    public function __construct(
        SqlServerPdoConnector $sqlPdoConnector,
        MysqlPdoConnector $mysqlPdoConnector,
        ContainerBagInterface $params
    ) {
        $this->sqlPdoConnector = $sqlPdoConnector;
        $this->mysqlPdoConnector = $mysqlPdoConnector;
        $this->params = $params;
    }

    public function save($entity): void
    {
        $envFilePath = $this->params->get('env.file');
        $envFile = file_get_contents($envFilePath);

        if ($entity->getType() === 'sqlServer') {
            $envFile = $this->replaceSqlServerEnviroment($envFile, $entity);
        } else {
            $envFile = $this->replaceMysqlServerEnviroment($envFile, $entity);
        }

        file_put_contents($envFilePath, $envFile);
    }

    private function replaceSqlServerEnviroment(string $envFile, ConnectionEntity $connectionEntity): string
    {
        $user = $connectionEntity->getUser();
        $password = $connectionEntity->getPassword();
        $address = $connectionEntity->getAddress();
        $database = $connectionEntity->getDatabase();
        $exposedPort = $connectionEntity->getExposedPort();

        $replacements = [];
        $replacements[0] = "SQL_SERVER_USER=$user";
        $replacements[1] = "SA_PASSWORD=\"$password\"";
        $replacements[2] = "SQL_SERVER_ADDRESS=\"$address\"";
        $replacements[3] = "SQL_SERVER_DATABASE=\"$database\"";
        $replacements[4] = "SQL_SERVER_EXPOSED_PORT=$exposedPort";

        return preg_replace($this::SQL_SERVER_PATTERNS, $replacements, $envFile);
    }

    private function replaceMysqlServerEnviroment(string $envFile, ConnectionEntity $connectionEntity): string
    {
        $user = $connectionEntity->getUser();
        $password = $connectionEntity->getPassword();
        $address = $connectionEntity->getAddress();
        $database = $connectionEntity->getDatabase();
        $exposedPort = $connectionEntity->getExposedPort();

        $replacements = [];
        $replacements[0] = "MYSQL_USER=$user";
        $replacements[1] = "MYSQL_PASSWORD=\"$password\"";
        $replacements[2] = "MYSQL_ADDRESS=\"$address\"";
        $replacements[3] = "MYSQL_DATABASE=\"$database\"";
        $replacements[4] = "MYSQL_EXPOSED_PORT=$exposedPort";

        return preg_replace($this::MYSQL_SERVER_PATTERNS, $replacements, $envFile);
    }
}
