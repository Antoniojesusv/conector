<?php

namespace App\DbConnectors;

use Exception;
use PDO;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class UwSqlServerPdoConnector extends PdoConnector
{
    protected function __construct(ContainerBagInterface $params)
    {
        $this->params = $params;
    }

    public function connect(): void
    {
        $dsn = $this->params->get('sql.microsoft.dsn');
        $dsn = rtrim($dsn, "\;");

        try {
            $this->connection = new PDO($dsn, '', '');
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->connection = null;
        }
    }
}
