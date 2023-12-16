<?php

declare(strict_types=1);
namespace App\Shared\Infrastructure\Pdo\Dbal;

use App\Shared\Infrastructure\Pdo\Dbal\Contract\PdoManager;

abstract class PdoBase implements PdoManager
{
    protected ?\PDO $connection = null;
    protected static $instances = [];

    abstract public function connect(): void;

    public function getConnection(): \PDO
    {
        return $this->connection;
    }

    public function reconnect(): void
    {
        $this->connection = null;
        $this->connect();
    }

    public function hasConnection(): bool
    {
        return !is_null($this->connection);
    }
}