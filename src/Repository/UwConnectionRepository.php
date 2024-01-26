<?php

namespace App\Repository;

use App\DbConnectors\Factories\PdoFactoryI;
use App\Model\Database\Entities\ConnectionBaseEntity;
use App\Model\Database\Entities\UwConnectionEntity;
use App\Model\Database\Factories\ConnectionFactoryI;
use App\Model\Database\Repositories\UwConnectionRepositoryI;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class UwConnectionRepository implements UwConnectionRepositoryI
{
    const SQL_SERVER_PATTERNS = [
        '/EUROWIN_DB_DATABASE=.*/',
        '/SERVER_SQL=.*/',
        '/EUROWIN_DB_AUTHENTICATION_METHOD=.*/'
    ];

    private ConnectionFactoryI $uWConnectionFactory;

    public function __construct(
        PdoFactoryI $sqlPdoFactory,
        ConnectionFactoryI $uWConnectionFactory,
        ContainerBagInterface $params
    ) {
        $this->sqlPdoFactory = $sqlPdoFactory;
        $this->sqlPdoConnector = $this->sqlPdoFactory->create();
        $this->uWConnectionFactory = $uWConnectionFactory;
        $this->params = $params;
    }

    public function save(ConnectionBaseEntity $entity): void
    {
        $envFilePath = $this->params->get('env.file');
        $envFile = file_get_contents($envFilePath);

        $envFile = $this->replaceSqlServerEnviroment($envFile, $entity);

        file_put_contents($envFilePath, $envFile);
    }

    public function get(): ConnectionBaseEntity
    {
        $connectionParams = $this->getParams();
        $connection = $this->uWConnectionFactory->create($connectionParams);
        return $connection;
    }

    private function getParams(): array
    {
        $connectionParams = [];

        $connectionParams['database'] = $this->params->get('sql.database');
        $connectionParams['server'] = $this->params->get('sql.server');
        $connectionParams['status'] = $this->sqlPdoConnector->hasConnection();
        $connectionParams['message'] = '';

        if (!$connectionParams['status']) {
            $connectionParams['message'] = $this->sqlPdoConnector->getMessage();
        }

        return $connectionParams;
    }

    private function replaceSqlServerEnviroment(string $envFile, UwConnectionEntity $connectionEntity): string
    {
        $database = $connectionEntity->getDatabase();
        $server = $connectionEntity->getServer();
        $authenticationMethod = 'uw';

        $replacements = [];
        $replacements[0] = "EUROWIN_DB_DATABASE=\"$database\"";
        $replacements[1] = "SERVER_SQL=\"$server\"";
        $replacements[2] = "EUROWIN_DB_AUTHENTICATION_METHOD=\"$authenticationMethod\"";

        return preg_replace($this::SQL_SERVER_PATTERNS, $replacements, $envFile);
    }
}
