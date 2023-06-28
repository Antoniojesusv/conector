<?php

declare(strict_types=1);
namespace App\Synchronisation\Infrastructure\Pdo\Dbal;

use PDO;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

abstract class PdoConnector
{
    protected ?PDO $connection = null;
    protected string $message = '';
    protected static $instances = [];

    abstract public function connect(): void;

    protected function __clone()
    {
    }

    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize a singleton.');
    }

    public static function getInstance(): PdoConnector
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
        }

        return self::$instances[$cls];
    }

    public static function getInstanceReadingEnvironmentVariables(ContainerBagInterface $params): PdoConnector
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static($params);
        }

        return self::$instances[$cls];
    }

    public function getConnection()
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

    public function getMessage(): string
    {
        return $this->message;
    }
}
