<?php

declare(strict_types=1);
namespace App\Shared\Infrastructure\Pdo\FactoryMethod;

use App\Shared\Infrastructure\Pdo\Dbal\ConnectorMysqlPdoManager;
use App\Shared\Infrastructure\Pdo\Dbal\Contract\PdoManager;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

final class ConnectorDbManager extends DbManager
{
    /**
     * Summary of create
     * @param \Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface $params
     * @return \App\Shared\Infrastructure\Pdo\Dbal\Contract\PdoManager
     */
    public function create(ContainerBagInterface $params): PdoManager
    {
        $user = $params->get('connector.mysql.user');
        $password = $params->get('connector.mysql.password');
        $dsn = $params->get('connector.mysql.dsn');
        $dsn = rtrim($dsn, "\;");

        $serverVersion = '8.0';

        $pdoManager = new ConnectorMysqlPdoManager($dsn, $user, $password, $serverVersion);

        parent::connect($pdoManager);

        return $pdoManager;
    }
}