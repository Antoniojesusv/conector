<?php

namespace App\DbConnectors;

use Exception;
use PDO;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class MysqlPdoConnector
{
    private ContainerBagInterface $params;
    private ?PDO $connection = null;
    private string $message = '';

    public function __construct(ContainerBagInterface $params)
    {
        $this->params = $params;
    }

    public function connect()
    {
        $user = $this->params->get('mysql.user');
        $password = $this->params->get('mysql.password');
        $dsn = $this->params->get('mysql.dsn');
        $dsn = rtrim($dsn, "\;");

        try {
            $this->connection = new PDO($dsn, $user, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->connection = null;
        }
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
