<?php

namespace App\DbConnectors\Factories;

use App\DbConnectors\PdoConnector;
use App\DbConnectors\SqlServerPdoConnector;
use App\DbConnectors\UwSqlServerPdoConnector;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class SqlServerPdoFactory implements PdoFactoryI
{
    public ContainerBagInterface $params;
    
    public function __construct(ContainerBagInterface $params)
    {
        $this->params = $params;
    }
    
    public function create(string $type = null): PdoConnector
    {
        if ($type === 'sqlServer') {
            $SqlConnection = SqlServerPdoConnector::getInstance($this->params);
            return $SqlConnection;
        }

        $SqlConnection = UwSqlServerPdoConnector::getInstance($this->params);

        return $SqlConnection;
    }
}
