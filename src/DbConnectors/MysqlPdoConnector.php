<?php

namespace App\DbConnectors;

use Exception;
use PDO;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class MysqlPdoConnector extends PdoConnector
{
    protected function __construct(ContainerBagInterface $params)
    {
        $this->params = $params;
    }

    public function connect(): void
    {
        $user = $this->params->get('mysql.user');
        $password = $this->params->get('mysql.password');
        $dsn = $this->params->get('mysql.dsn');
        $dsn = rtrim($dsn, "\;");

        try {
            $this->connection = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT, 2
            ]);
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->connection = null;
        }
    }
}
