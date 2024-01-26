<?php
namespace App\Connection\Infrastructure\Persistance\Pdo;

use App\Connection\Application\DataTransformer\ConnectionRepositoryDataTransformer;
use App\Connection\Application\DataTransformer\ConnectionSecurityRepositoryDataTransformer;
use App\Connection\Domain\Connection;
use App\Shared\Infrastructure\Pdo\Dbal\Contract\PdoManager;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class ConnectionRepository
{
    const TYPES = ['sqlServer', 'mysqlServer'];

    const SQL_SERVER_PATTERNS = [
        '/EUROWIN_DB_USER=.*/',
        '/EUROWIN_DB_PASSWORD=.*/',
        '/EUROWIN_DB_ADDRESS=.*/',
        '/EUROWIN_DB_DATABASE=.*/',
        '/EUROWIN_DB_EXPOSED_PORT=.*/',
    ];

    const MYSQL_SERVER_PATTERNS = [
        '/BODECALL_DB_USER=.*/',
        '/BODECALL_DB_PASSWORD=.*/',
        '/BODECALL_DB_ADDRESS=.*/',
        '/BODECALL_DB_DATABASE=.*/',
        '/BODECALL_DB_EXPOSED_PORT=.*/'
    ];

    public function __construct(
        private ContainerBagInterface $params,
        private PdoManager $sqlPdoManager,
        private PdoManager $mysqlPdoManager,
        private ConnectionSecurityRepositoryDataTransformer $connectionSecurityRepositoryDataTransformer,
        private ConnectionRepositoryDataTransformer $connectionRepositoryDataTransformer
    ) {
        $this->mysqlPdoManager = $mysqlPdoManager;
        $this->sqlPdoManager = $sqlPdoManager;
        $this->connectionSecurityRepositoryDataTransformer = $connectionSecurityRepositoryDataTransformer;
        $this->connectionRepositoryDataTransformer = $connectionRepositoryDataTransformer;
        // $this->sqlPdoManager->connect();
        // $this->mysqlPdoManager->connect();
        $this->params = $params;
    }

    public function update(Connection $connection): void
    {
        $envFilePath = $this->params->get('env.file');
        $envFile = file_get_contents($envFilePath);

        if ($connection->type() === 'sqlServer') {
            $envFile = $this->replaceSqlServerEnviroment($envFile, $connection);
        } else {
            $envFile = $this->replaceMysqlServerEnviroment($envFile, $connection);
        }

        file_put_contents($envFilePath, $envFile);
    }

    public function getAll(): array
    {
        return [
            $this->connectionSecurityRepositoryDataTransformer->transform($this->getParamsByType('mysqlServer')),
            $this->connectionSecurityRepositoryDataTransformer->transform($this->getParamsByType('sqlServer'))
        ];
    }

    public function getByType(string $type): Connection
    {
        $connectionParams = $this->getParamsByType($type);
        $connection = $this->connectionRepositoryDataTransformer->transform($connectionParams);
        return $connection;
    }

    private function getParamsByType(string $type): array
    {
        preg_match('/([\w\d]*)Server/', $type, $match);

        $type = $match[1];

        $this->isValidType($match);

        $connectionParams = [];

        $connectionParams['user'] = $this->params->get("$type.user");
        $connectionParams['password'] = $this->params->get("$type.password");
        $connectionParams['address'] = $this->params->get("$type.address");
        $connectionParams['database'] = $this->params->get("$type.database");
        $connectionParams['exposedPort'] = $this->params->get("$type.exposed.port");
        $connectionParams['type'] = 'mysqlServer';
        $connectionParams['status'] = $this->mysqlPdoManager->hasConnection();

        if ($type === 'sql') {
            $connectionParams['type'] = 'sqlServer';
            $connectionParams['status'] = $this->sqlPdoManager->hasConnection();
        }

        // $connectionParams['message'] = '';

        // if (!$connectionParams['status'] && $type === 'sql') {
        //     $connectionParams['message'] = $this->mysqlPdoManager->getMessage();
        // }

        // if (!$connectionParams['status'] && $type === 'mysql') {
        //     $connectionParams['message'] = $this->mysqlPdoManager->getMessage();
        // }


        return $connectionParams;
    }

    private function isValidType(array $match)
    {

        if (empty($match[0]) | !in_array($match[0], self::TYPES)) {
            throw new \Exception("The type ($match[0]) is not a valid type");
        }
    }

    private function replaceSqlServerEnviroment(string $envFile, Connection $connection): string
    {
        $user = $connection->user();
        $password = $connection->password();
        $address = $connection->address();
        $database = $connection->databaseName();
        $exposedPort = $connection->port();

        $replacements = [];
        $replacements[0] = "EUROWIN_DB_USER=$user";
        $replacements[1] = "EUROWIN_DB_PASSWORD=\"$password\"";
        $replacements[2] = "EUROWIN_DB_ADDRESS=\"$address\"";
        $replacements[3] = "EUROWIN_DB_DATABASE=\"$database\"";
        $replacements[4] = "EUROWIN_DB_EXPOSED_PORT=$exposedPort";

        return preg_replace($this::SQL_SERVER_PATTERNS, $replacements, $envFile);
    }

    private function replaceMysqlServerEnviroment(string $envFile, Connection $connection): string
    {
        $user = $connection->user();
        $password = $connection->password();
        $address = $connection->address();
        $database = $connection->databaseName();
        $exposedPort = $connection->port();

        $replacements = [];
        $replacements[0] = "BODECALL_DB_USER=$user";
        $replacements[1] = "BODECALL_DB_PASSWORD=\"$password\"";
        $replacements[2] = "BODECALL_DB_ADDRESS=\"$address\"";
        $replacements[3] = "BODECALL_DB_DATABASE=\"$database\"";
        $replacements[4] = "BODECALL_DB_EXPOSED_PORT=$exposedPort";

        return preg_replace($this::MYSQL_SERVER_PATTERNS, $replacements, $envFile);
    }
}