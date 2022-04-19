<?php

namespace App\Model\Synchronisation;

use Exception;

class ArticleEntity
{
    private string $code;
    private string $imageName;
    private string $image;

    public function __construct(
        string $code,
        string $imageName,
        string $image,
    ) {
        $this->setCode($code);
        $this->setImageName($imageName);
        $this->setImage($image);
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getImageName(): string
    {
        return $this->imageName;
    }

    public function setImageName(string $imageName): void
    {
        if (empty($imageName)) {
            throw new Exception('The image name cannot be empty');
        }

        $this->imageName = $imageName;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): void
    {
        if (empty($image)) {
            throw new Exception('The image cannot be empty');
        }

        $this->image = $image;
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'imageName' => $this->imageName,
            'temporaryImageFile' => $this->getTemporaryImageFile(),
            'image' => $this->image,
        ];
    }

    public function __toString(): string
    {
        return json_encode([
            'code' => $this->code,
            'image' => $this->image
        ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    private function getTemporaryImageFile(): string
    {
        $imageName = $this->imageName;
        return "temporaryImages/$imageName";
    }
}
