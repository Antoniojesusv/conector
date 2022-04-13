<?php

namespace App\Model\Database\Repositories;

use App\Model\Database\Entities\ConnectionBaseEntity;

interface UwConnectionRepositoryI extends ConnectionRepositoryI
{
    public function get(): ConnectionBaseEntity;
}
