<?php

declare(strict_types=1);
namespace App\Shared\Infrastructure\Pdo\FactoryMethod;

use App\Shared\Infrastructure\Pdo\Dbal\Contract\PdoManager;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

abstract class DbManager
{
    abstract public function create(ContainerBagInterface $params): PdoManager;

    /**
     * Summary of connect
     * @param \App\Shared\Infrastructure\Pdo\Dbal\Contract\PdoManager $pdoManager
     * @return \PDO
     */
    public function connect(PdoManager $pdoManager): void
    {
        if (!$pdoManager->hasConnection()) {
            $pdoManager->connect();
        }
    }
}