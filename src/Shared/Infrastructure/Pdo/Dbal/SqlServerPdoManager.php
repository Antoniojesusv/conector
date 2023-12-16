<?php

declare(strict_types=1);
namespace App\Shared\Infrastructure\Pdo\Dbal;

use Exception;
use PDO;

class SqlServerPdoManager extends PdoBase
{
    public function __construct(
        private string $dsn = "",
        private string $username = "",
        private string $password = ""
    ) {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
    }

    public function connect(): void
    {
        try {
            $this->connection = new PDO($this->dsn, $this->username, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->connection = null;
        }
    }
}