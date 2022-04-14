<?php

namespace App\Model\Server;

use Exception;

class ServerEntity
{
    private string $photosPath;

    public function __construct(
        string $photosPath
    ) {
        $this->setPhotosPath($photosPath);
    }

    public function getPhotosPath(): string
    {
        return $this->photosPath;
    }

    public function setPhotosPath(string $photosPath): void
    {
        if (empty($photosPath)) {
            throw new Exception('The photos path cannot be empty');
        }

        $this->photosPath = $photosPath;
    }
}
