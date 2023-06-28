<?php

declare(strict_types=1);
namespace App\Synchronisation\Infrastructure\Pdo\Dbal;

use Exception;
use PDO;

class ConectorPdoConnector extends PdoConnector
{
    public function connect(): void
    {
        $user = 'antonio';
        $password = 'antonio123';
        $address = '192.168.0.96';
        $dbName = 'conector';
        $port = '3309';
        $dsn = "mysql:host={$address};dbname={$dbName};charset=utf8mb4;port={$port}?serverVersion=8.0";
        $dsn = rtrim($dsn, "\;");

        try {
            $this->connection = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT, 2
            ]);

            $emulate_prepares_below_version = '8.0';
            $serverversion = $this->connection->getAttribute(PDO::ATTR_SERVER_VERSION);
            $emulate_prepares = (version_compare($serverversion, $emulate_prepares_below_version, '<'));
            $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, $emulate_prepares);
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->connection = null;
        }
    }
}
