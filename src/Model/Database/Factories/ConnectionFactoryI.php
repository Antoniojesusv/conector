<?php

namespace App\Model\Database\Factories;

use App\Model\Database\Entities\ConnectionBaseEntity;

interface ConnectionFactoryI
{
    public function create(array $params): ConnectionBaseEntity;
}
