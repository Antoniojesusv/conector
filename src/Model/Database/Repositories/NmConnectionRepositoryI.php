<?php

namespace App\Model\Database\Repositories;

use App\Model\Database\Entities\ConnectionBaseEntity;

interface NmConnectionRepositoryI extends ConnectionRepositoryI
{
    public function getByType(string $type): ConnectionBaseEntity;
}
