<?php

declare(strict_types=1);
namespace App\Synchronisation\Infrastructure\Pdo\Dbal;

use Exception;
use PDO;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class EurowinPdoConnector extends PdoConnector
{
    protected function __construct(private ContainerBagInterface $params)
    {
        $this->params = $params;
    }

    public function connect(): void
    {
        $user = $this->params->get('sql.user');
        $password = $this->params->get('sql.password');
        $dsn = $this->params->get('sql.dsn');
        $dsn = rtrim($dsn, "\;");

        try {
            $this->connection = new PDO($dsn, $user, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->connection = null;
        }
    }
}
