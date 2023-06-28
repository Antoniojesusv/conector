<?php

declare(strict_types=1);
namespace App\Synchronisation\Infrastructure\Pdo\FactoryMethod;

use App\Synchronisation\Infrastructure\Pdo\Dbal\EurowinPdoConnector;
use App\Synchronisation\Infrastructure\Pdo\Dbal\PdoConnector;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

final class EurowinPdoConnectorManager extends PdoConnectorManager
{
    public function __construct(private ContainerBagInterface $params)
    {
    }

    public function create(): PdoConnector
    {
        return EurowinPdoConnector::getInstanceReadingEnvironmentVariables($this->params);
    }
}
