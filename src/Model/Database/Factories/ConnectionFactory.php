<?php

namespace App\Model\Database\Factories;

use App\Model\Database\Entities\ConnectionEntity;

abstract class ConnectionFactory
{
    abstract public function create(array $params): ConnectionEntity;
}
