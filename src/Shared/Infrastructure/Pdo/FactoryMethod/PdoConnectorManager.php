<?php

declare(strict_types=1);
namespace App\Synchronisation\Infrastructure\Pdo\FactoryMethod;

use App\Synchronisation\Infrastructure\Pdo\Dbal\PdoConnector;
use PDO;

abstract class PdoConnectorManager
{
    abstract public function create(): PdoConnector;

    public function connect(): PDO
    {
        $pdoConnector = $this->create();
        $pdoConnector->hasConnection() ? $pdoConnector->reconnect() : $pdoConnector->connect();
        return $pdoConnector->getConnection();
    }
}
