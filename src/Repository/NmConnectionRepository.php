<?php

namespace App\Repository;

use App\DbConnectors\Factories\PdoFactoryI;
use App\Model\Database\Entities\ConnectionBaseEntity;
use App\Model\Database\Entities\NmConnectionEntity;
use App\Model\Database\Factories\ConnectionFactoryI;
use App\Model\Database\Repositories\NmConnectionRepositoryI;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class NmConnectionRepository implements NmConnectionRepositoryI
{
    const TYPES = ['sqlServer', 'mysqlServer'];

    const SQL_SERVER_PATTERNS = [
        '/EUROWIN_DB_USER=.*/',
        '/EUROWIN_DB_PASSWORD=.*/',
        '/EUROWIN_DB_ADDRESS=.*/',
        '/EUROWIN_DB_DATABASE=.*/',
        '/EUROWIN_DB_EXPOSED_PORT=.*/',
        '/EUROWIN_DB_AUTHENTICATION_METHOD=.*/'
    ];

    const MYSQL_SERVER_PATTERNS = [
        '/BODECALL_DB_USER=.*/',
        '/BODECALL_DB_PASSWORD=.*/',
        '/BODECALL_DB_ADDRESS=.*/',
        '/BODECALL_DB_DATABASE=.*/',
        '/BODECALL_DB_EXPOSED_PORT=.*/'
    ];

    private PdoFactoryI $sqlPdoFactory;
    private PdoFactoryI $mysqlPdoFactory;
    private ConnectionFactoryI $nmConnectionFactory;
    private ContainerBagInterface $params;

    public function __construct(
        PdoFactoryI $sqlPdoFactory,
        PdoFactoryI $mysqlPdoFactory,
        ConnectionFactoryI $nmConnectionFactory,
        ContainerBagInterface $params,
    ) {
        $this->sqlPdoFactory = $sqlPdoFactory;
        $this->mysqlPdoFactory = $mysqlPdoFactory;
        $this->sqlPdoConnector = $this->sqlPdoFactory->create('sqlServer');
        $this->mysqlPdoConnector = $this->mysqlPdoFactory->create();
        $this->nmConnectionFactory = $nmConnectionFactory;
        $this->params = $params;
    }

    public function save(ConnectionBaseEntity $entity): void
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

    public function getByType(string $type): ConnectionBaseEntity
    {
        $connectionParams = $this->getParamsByType($type);
        $connection = $this->nmConnectionFactory->create($connectionParams);
        return $connection;
    }

    private function getParamsByType(string $type): array
    {
        preg_match('/([\w\d]*)Server/', $type, $match);

        $type = $match[1];

        if (empty($match[0]) | !in_array($match[0], $this::TYPES)) {
            throw new Exception("The type ($match[0]) is not a valid type");
        }

        $connectionParams = [];

        $connectionParams['user'] = $this->params->get("$type.user");
        $connectionParams['password'] = $this->params->get("$type.password");
        $connectionParams['address'] = $this->params->get("$type.address");
        $connectionParams['database'] = $this->params->get("$type.database");
        $connectionParams['exposedPort'] = $this->params->get("$type.exposed.port");
        $connectionParams['type'] = 'mysqlServer';
        $connectionParams['status'] = $this->mysqlPdoConnector->hasConnection();

        if ($type === 'sql') {
            $connectionParams['type'] = 'sqlServer';
            $connectionParams['status'] = $this->sqlPdoConnector->hasConnection();
        }

        $connectionParams['message'] = '';

        if (!$connectionParams['status'] && $type === 'sql') {
            $connectionParams['message'] = $this->sqlPdoConnector->getMessage();
        }

        if (!$connectionParams['status'] && $type === 'mysql') {
            $connectionParams['message'] = $this->mysqlPdoConnector->getMessage();
        }


        return $connectionParams;
    }

    private function replaceSqlServerEnviroment(string $envFile, NmConnectionEntity $connectionEntity): string
    {
        $user = $connectionEntity->getUser();
        $password = $connectionEntity->getPassword();
        $address = $connectionEntity->getAddress();
        $database = $connectionEntity->getDatabase();
        $exposedPort = $connectionEntity->getExposedPort();
        $authenticationMethod = 'nm';

        $replacements = [];
        $replacements[0] = "EUROWIN_DB_USER=$user";
        $replacements[1] = "EUROWIN_DB_PASSWORD=\"$password\"";
        $replacements[2] = "EUROWIN_DB_ADDRESS=\"$address\"";
        $replacements[3] = "EUROWIN_DB_DATABASE=\"$database\"";
        $replacements[4] = "EUROWIN_DB_EXPOSED_PORT=$exposedPort";
        $replacements[5] = "EUROWIN_DB_AUTHENTICATION_METHOD=\"$authenticationMethod\"";

        return preg_replace($this::SQL_SERVER_PATTERNS, $replacements, $envFile);
    }

    private function replaceMysqlServerEnviroment(string $envFile, NmConnectionEntity $connectionEntity): string
    {
        $user = $connectionEntity->getUser();
        $password = $connectionEntity->getPassword();
        $address = $connectionEntity->getAddress();
        $database = $connectionEntity->getDatabase();
        $exposedPort = $connectionEntity->getExposedPort();

        $replacements = [];
        $replacements[0] = "BODECALL_DB_USER=$user";
        $replacements[1] = "BODECALL_DB_PASSWORD=\"$password\"";
        $replacements[2] = "BODECALL_DB_ADDRESS=\"$address\"";
        $replacements[3] = "BODECALL_DB_DATABASE=\"$database\"";
        $replacements[4] = "BODECALL_DB_EXPOSED_PORT=$exposedPort";

        return preg_replace($this::MYSQL_SERVER_PATTERNS, $replacements, $envFile);
    }
}
