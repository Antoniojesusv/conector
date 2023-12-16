<?php

declare(strict_types=1);
namespace App\Shared\Infrastructure\Pdo\FactoryMethod;

use App\Shared\Infrastructure\Pdo\Dbal\Contract\PdoManager;
use App\Shared\Infrastructure\Pdo\Dbal\SqlServerPdoManager;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

final class EurowinDbManager extends DbManager
{
    /**
     * Summary of create
     * @param \Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface $params
     * @return \App\Shared\Infrastructure\Pdo\Dbal\Contract\PdoManager
     */
    public function create(ContainerBagInterface $params): PdoManager
    {
        $user = $params->get('sql.user');
        $password = $params->get('sql.password');
        $dsn = $params->get('sql.dsn');
        $dsn = rtrim($dsn, "\;");

        $pdoManager = new SqlServerPdoManager($dsn, $user, $password);

        parent::connect($pdoManager);

        return $pdoManager;
    }
}