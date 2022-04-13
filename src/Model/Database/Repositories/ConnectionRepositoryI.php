<?php

namespace App\Model\Database\Repositories;

use App\Model\Database\Entities\ConnectionBaseEntity;

interface ConnectionRepositoryI
{
    public function save(ConnectionBaseEntity $connection): void;
}
