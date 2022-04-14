<?php

namespace App\Model\Server;

use App\Model\Server\ServerEntity;

interface ServerRepositoryI
{
    public function save(ServerEntity $connection): void;
    public function get(): ServerEntity;
}
