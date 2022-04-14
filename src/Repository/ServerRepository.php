<?php

namespace App\Repository;

use App\Model\Server\ServerEntity;
use App\Model\Server\ServerRepositoryI;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class ServerRepository implements ServerRepositoryI
{
    const SQL_SERVER_PATTERNS = [
        '/SERVER_PHOTOS_PATH=.*/'
    ];

    public function __construct(
        ContainerBagInterface $params
    ) {
        $this->params = $params;
    }

    public function save(ServerEntity $entity): void
    {
        $envFilePath = $this->params->get('env.file');
        $envFile = file_get_contents($envFilePath);

        $envFile = $this->replaceSqlServerEnviroment($envFile, $entity);

        file_put_contents($envFilePath, $envFile);
    }

    public function get(): ServerEntity
    {
        $serverParams = $this->getParams();

        [
            'photosPath' => $photosPath
        ] = $serverParams;

        $serverEntity = new ServerEntity($photosPath);
        return $serverEntity;
    }

    private function getParams(): array
    {
        $connectionParams = [];
        
        $connectionParams['photosPath'] = $this->params->get('server.photos.path');

        return $connectionParams;
    }

    private function replaceSqlServerEnviroment(string $envFile, ServerEntity $serverEntity): string
    {
        $photosPath = $serverEntity->getPhotosPath();

        $replacements = [
            "SERVER_PHOTOS_PATH=\"$photosPath\""
        ];

        return preg_replace($this::SQL_SERVER_PATTERNS, $replacements, $envFile);
    }
}
