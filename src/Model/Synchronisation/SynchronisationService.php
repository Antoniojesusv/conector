<?php

namespace App\Model\Synchronisation;

use App\Services\DirectoryReadService;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Error;
use Generator;

class SynchronisationService
{
    private ContainerBagInterface $params;
    private DirectoryReadService $directoryReadService;
    private ArticleRepositoryI $articleRepository;
    
    public function __construct(
        ContainerBagInterface $params,
        DirectoryReadService $directoryReadService,
        ArticleRepositoryI $articleRepository
    ) {
        $this->params = $params;
        $this->directoryReadService = $directoryReadService;
        $this->articleRepository = $articleRepository;
    }

    public function synchronise(): void
    {
        $imagesPath = $this->getImagesFolderPath();

        if (!is_dir($imagesPath)) {
            throw new Error("The folder '$imagesPath' does not exist");
        }

        $filesPath = $this->directoryReadService->readFilesFromDirectory($imagesPath);

        if (empty($filesPath)) {
            throw new Error("The directory '$imagesPath' is empty");
        }

        $imagesFilesNameList = $this->getImagesFilesName($filesPath);
        $imagesFilesNameAssociativeList = $this->buildImagesFilesNameAssociativeList($imagesFilesNameList);
        $entityList = $this->buildEntityList($imagesFilesNameAssociativeList);
        $entityGenerator = $this->copyImageToTemporaryFolder($entityList);

        $this->articleRepository->save($entityGenerator);
    }

    private function getEurowinFolder(): string
    {
        $folderPath = $this->params->get('server.photos.path');
        // $folderPath = $this->params->get('server.eurowin.photos.path');

        preg_match("/^C:/", $folderPath, $match);

        if (empty($match[0])) {
            return $folderPath;
        }

        preg_match("/^C:(.*)/", $folderPath, $match);

        $serverName = "\\\\DOTEW";
        $path = strtoupper($match[1]);

        $folderPath = "$serverName$path";
        return $folderPath;
    }

    public function getImagesFolderPath(): string
    {
        return $this->params->get('server.photos.path');
    }

    public function deleteTemporaryImages(): void
    {
        $temporaryFolder = $this->params->get('images.temporary.folder');
        $filesPath = $this->directoryReadService->readFilesFromDirectory($temporaryFolder);

        foreach ($filesPath as $file) {
            if (!unlink($file)) {
                throw new Error("There was a error deleting the file $file");
            }
        }
    }

    private function copyImageToTemporaryFolder(iterable $entityList): Generator
    {
        $temporaryFolder = $this->params->get('images.temporary.folder');

        foreach ($entityList as $entity) {
            $source = $entity->getImage();
            $fileName = $entity->getImageName();
            $destination = $temporaryFolder . DIRECTORY_SEPARATOR . $fileName;

            if (!copy($source, $destination)) {
                throw new Error("The file '$fileName' cannot be copied.");
            }

            yield $entity;
        }
    }

    private function getImagesFilesName(array $filesPath): Generator
    {
        // $imagesFilesNameList = [];
        
        foreach ($filesPath as $filePath) {
            $filePathSanitized = preg_replace('/\s/', '', $filePath);
            preg_match('/[\w\d-]+\].+/', $filePathSanitized, $match);

            if ($this->wasSanitized($filePath)) {
                $this->renameImageFile($filePath, $filePathSanitized);
            }
            // $imagesFilesNameList[] = $match[0];

            if (empty($match[0])) {
                continue;
                // throw new Error("The file '$filePath' cannot be synchronized.");
            }

            yield $match[0];
        }

        // return $imagesFilesNameList;
    }

    private function wasSanitized(string $filePath): bool
    {
        preg_match('/\s/', $filePath, $match);
        return !empty($match[0]);
    }

    private function renameImageFile(string $filePath, string $filePathSanitized): void
    {
        rename($filePath, $filePathSanitized);
    }

    private function buildImagesFilesNameAssociativeList(Iterable $imagesFilesNameList): Generator
    {
        // $imagesFilesNameAssociativeList = [];

        foreach ($imagesFilesNameList as $fileName) {
            $code = $this->getCodeFromFileName($fileName);
            $imagePath = $this->getImagesFolderPath() . DIRECTORY_SEPARATOR . $fileName;
            $eurowinImagePath = $this->getEurowinFolder() . DIRECTORY_SEPARATOR . $fileName;
            // $imagesFilesNameAssociativeList[] = ['code' => $code, 'imageName' => $fileName, 'imagen' => $imagePath];
            yield ['code' => $code, 'imageName' => $fileName, 'imagen' => $imagePath, 'eurowinImage' => $eurowinImagePath];
        }

        // return $imagesFilesNameAssociativeList;
    }

    private function buildEntityList(Iterable $imagesFilesNameAssociativeList): Generator
    {
        // $entityList = [];
        
        foreach ($imagesFilesNameAssociativeList as ['code' => $code, 'imageName' => $fileName, 'imagen' => $imagePath, 'eurowinImage' => $eurowinImagePath]) {
            // $entityList[] = new ArticleEntity($code, $fileName, $imagePath);
            yield new ArticleEntity($code, $fileName, $imagePath, $eurowinImagePath);
        }

        // return $entityList;
    }

    private function getCodeFromFileName(string $fileName): string
    {
        preg_match('/^[\w\d]+/', $fileName, $match);
        return $match[0];
    }

    // private function getNameFromFileName(string $fileName): string
    // {
    //     preg_match('/\](.+)/', $fileName, $match);
    //     return $match[1];
    // }
}
