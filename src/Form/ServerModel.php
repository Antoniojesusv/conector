<?php

namespace App\Form;

use App\Model\Server\ServerEntity;
use Symfony\Component\Validator\Constraints as Assert;

class ServerModel
{
    /**
     * @Assert\NotBlank
     * @Assert\Callback({"App\Form\PhotosPathValidator", "validate"})
     * @Assert\Type("string")
     */
    private string $photosPath;

    public function getPhotosPath(): string
    {
        return $this->photosPath;
    }

    public function setPhotosPath(string $photosPath = ''): void
    {
        $this->photosPath = $photosPath;
    }

    public function setData(ServerEntity $server): void
    {
        $this->photosPath = $server->getPhotosPath();
    }

    public function toArray(): array
    {
        return [
            'photosPath' => $this->photosPath
        ];
    }
}
