<?php

namespace App\Model\Server;

use App\Model\Server\ServerEntity;
use App\Model\Server\ServerRepositoryI;

class ServerService
{
    private ServerRepositoryI $serverRepository;

    public function __construct(
        ServerRepositoryI $serverRepository
    ) {
        $this->serverRepository = $serverRepository;
    }

    public function persist(array $data): void
    {
        [
            'photosPath' => $photosPath,
        ] = $data;

        $serverEntity = new ServerEntity($photosPath);

        $this->serverRepository->save($serverEntity);
    }
}
