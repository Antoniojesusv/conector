<?php

declare(strict_types=1);
namespace App\Shared\Infrastructure\Pdo\FactoryMethod;

use App\Shared\Infrastructure\Pdo\Dbal\Contract\PdoManager;
use App\Shared\Infrastructure\Pdo\Dbal\MysqlPdoManager;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

final class BodecallDbManager extends DbManager
{
    /**
     * Summary of create
     * @param \Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface $params
     * @return \App\Shared\Infrastructure\Pdo\Dbal\Contract\PdoManager
     */
    public function create(ContainerBagInterface $params): PdoManager
    {
        $user = $params->get('mysql.user');
        $password = $params->get('mysql.password');
        $dsn = $params->get('mysql.dsn');
        $dsn = rtrim($dsn, "\;");

        $serverVersion = '5.1.17';

        $pdoManager = new MysqlPdoManager($dsn, $user, $password, $serverVersion);

        parent::connect($pdoManager);

        return $pdoManager;
    }
}