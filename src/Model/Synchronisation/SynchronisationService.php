<?php

namespace App\Model\Synchronisation;

use App\Services\DirectoryReadService;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Error;

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
        $this->copyImageToTemporaryFolder($entityList);

        $this->articleRepository->save($entityList);
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

    private function copyImageToTemporaryFolder(array $entityList): void
    {
        $temporaryFolder = $this->params->get('images.temporary.folder');

        foreach ($entityList as $entity) {
            $source = $entity->getImage();
            $fileName = $entity->getImageName();
            $destination = $temporaryFolder . DIRECTORY_SEPARATOR . $fileName;

            if (!copy($source, $destination)) {
                throw new Error("The file '$fileName' cannot be copied.");
            }
        }
    }

    private function getImagesFilesName(array $filesPath): array
    {
        $imagesFilesNameList = [];
        
        foreach ($filesPath as $filePath) {
            preg_match('/\d+\].+/', $filePath, $match);
            $imagesFilesNameList[] = $match[0];
        }

        return $imagesFilesNameList;
    }

    private function buildEntityList(array $imagesFilesNameAssociativeList): array
    {
        $entityList = [];
        
        foreach ($imagesFilesNameAssociativeList as ['code' => $code, 'imageName' => $fileName, 'imagen' => $imagePath]) {
            $entityList[] = new ArticleEntity($code, $fileName, $imagePath);
        }

        return $entityList;
    }

    private function buildImagesFilesNameAssociativeList(array $imagesFilesNameList): array
    {
        $imagesFilesNameAssociativeList = [];

        foreach ($imagesFilesNameList as $fileName) {
            $code = $this->getCodeFromFileName($fileName);
            $imagePath = $this->getImagesFolderPath() . DIRECTORY_SEPARATOR . $fileName;
            $imagesFilesNameAssociativeList[] = ['code' => $code, 'imageName' => $fileName, 'imagen' => $imagePath];
        }

        return $imagesFilesNameAssociativeList;
    }

    private function getCodeFromFileName(string $fileName): string
    {
        preg_match('/\d+/', $fileName, $match);
        return $match[0];
    }

    // private function getNameFromFileName(string $fileName): string
    // {
    //     preg_match('/\](.+)/', $fileName, $match);
    //     return $match[1];
    // }
}
