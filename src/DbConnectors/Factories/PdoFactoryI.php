<?php

namespace App\DbConnectors\Factories;

use App\DbConnectors\PdoConnector;

interface PdoFactoryI
{
    public function create(string $type = null): PdoConnector;
}
