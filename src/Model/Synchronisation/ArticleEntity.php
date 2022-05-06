<?php

namespace App\Model\Synchronisation;

use Exception;

class ArticleEntity
{
    private string $code;
    private string $imageName;
    private string $image;
    private string $eurowinImage;

    public function __construct(
        string $code,
        string $imageName,
        string $image,
        string $eurowinImage
    ) {
        $this->setCode($code);
        $this->setImageName($imageName);
        $this->setImage($image);
        $this->setEurowinImage($eurowinImage);
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

    public function getEurowinImage(): string
    {
        return $this->eurowinImage;
    }

    public function setEurowinImage(string $eurowinImage): void
    {
        if (empty($eurowinImage)) {
            throw new Exception('The eurowinImage cannot be empty');
        }

        $this->eurowinImage = $eurowinImage;
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'imageName' => $this->imageName,
            'temporaryImageFile' => $this->getTemporaryImageFile(),
            'image' => $this->image,
            'eurowinImage' => $this->eurowinImage
        ];
    }

    public function __toString(): string
    {
        return json_encode([
            'code' => $this->code,
            'image' => $this->image,
            'eurowinImage' => $this->eurowinImage
        ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    private function getTemporaryImageFile(): string
    {
        $imageName = $this->imageName;
        return "temporaryImages/$imageName";
    }
}
