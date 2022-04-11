<?php

namespace App\DbConnectors\Factories;

use App\DbConnectors\MysqlPdoConnector;
use App\DbConnectors\PdoConnector;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class MysqlPdoFactory implements PdoFactoryI
{
    public ContainerBagInterface $params;
    
    public function __construct(ContainerBagInterface $params)
    {
        $this->params = $params;
    }
    
    public function create(string $type = null): PdoConnector
    {
        $MpConnection = MysqlPdoConnector::getInstance($this->params);
        return $MpConnection;
    }
}
