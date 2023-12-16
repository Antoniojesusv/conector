<?php

declare(strict_types=1);
namespace App\Shared\Infrastructure\Pdo\Dbal;

use Exception;
use PDO;

class MysqlPdoManager extends PdoBase
{
    public function __construct(
        private string $dsn = "",
        private string $username = "",
        private string $password = "",
        private string $mysqlVersion = "5.1.17"
    ) {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->mysqlVersion = $mysqlVersion;
    }

    public function connect(): void
    {
        try {
            $this->connection = new PDO($this->dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT,
                2
            ]);

            $emulate_prepares_below_version = $this->mysqlVersion;
            $serverVersion = $this->connection->getAttribute(PDO::ATTR_SERVER_VERSION);
            $emulate_prepares = (version_compare($serverVersion, $emulate_prepares_below_version, '<'));
            $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, $emulate_prepares);
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->connection = null;
        }
    }
}