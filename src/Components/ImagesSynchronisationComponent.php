<?php

namespace App\Components;

use App\Model\Synchronisation\SynchronisationService;
use App\Repository\ArticleRepository;
use Error;
use Exception;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('images_synchronisation')]
class ImagesSynchronisationComponent
{
    use DefaultActionTrait;

    #[LiveProp()]
    public bool $hide = true;

    #[LiveProp()]
    public string $messageError = '';

    #[LiveProp()]
    public string $imagesFolderPath = '';

    #[LiveProp()]
    public array $images = [];

    private SynchronisationService $synchronisationService;
    private ArticleRepository $articleRepository;

    public function __construct(
        SynchronisationService $synchronisationService,
        ArticleRepository $articleRepository
    ) {
        $this->synchronisationService = $synchronisationService;
        $this->articleRepository = $articleRepository;
    }

    public function mount()
    {
        $this->imagesFolderPath = $this->synchronisationService->getImagesFolderPath();
    }

    #[LiveAction]
    public function synchronise(): void
    {
        try {
            $this->messageError = '';
            $this->showLogDisplay();
            // $this->synchronisationService->deleteTemporaryImages();
            $this->synchronisationService->synchronise();
            $this->images = $this->articleRepository->getAll();
        } catch (Error $error) {
            $this->hideLogDisplay();
            $this->messageError = $error->getMessage();
        } catch (Exception $error) {
            $this->hideLogDisplay();
            $this->messageError = $error->getMessage();
        }
    }

    private function hideLogDisplay(): void
    {
        $this->hide = true;
    }

    private function showLogDisplay(): void
    {
        $this->hide = false;
    }

    private function toogleLogDisplay(): void
    {
        $this->hide = !$this->hide;
    }
}
