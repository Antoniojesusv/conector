<?php

namespace App\Components;

use App\Model\Synchronisation\ArticlesSynchronisationService;
use App\Repository\ArticleProductRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Error;

#[AsLiveComponent('articles_synchronisation')]
class ArticlesSynchronisationComponent
{
    use DefaultActionTrait;

    #[LiveProp()]
    public bool $hide = true;

    // #[LiveProp()]
    // public bool $progressVisibility = true;

    #[LiveProp()]
    public string $messageError = '';

    #[LiveProp()]
    public array $articles = [];

    private ArticlesSynchronisationService $synchronisationService;
    private ArticleProductRepository $articleRepository;

    public function __construct(
        ArticlesSynchronisationService $synchronisationService,
        ArticleProductRepository $articleRepository
    ) {
        $this->synchronisationService = $synchronisationService;
        $this->articleRepository = $articleRepository;
    }

    #[LiveAction]
    public function synchronise(): void
    {
        try {
            $this->messageError = '';
            // $this->showProgressVisibility();
            $this->showLogDisplay();
            $this->synchronisationService->synchronise();
            $this->articles = $this->articleRepository->getAll();
        } catch (Error $error) {
            // $this->hideProgressVisibility();
            $this->hideLogDisplay();
            $this->messageError = $error->getMessage();
        }
    }

    private function showLogDisplay(): void
    {
        $this->hide = false;
    }

    private function hideLogDisplay(): void
    {
        $this->hide = true;
    }

    private function toogleLogDisplay(): void
    {
        $this->hide = !$this->hide;
    }

    private function showProgressVisibility(): void
    {
        $this->progressVisibility = true;
    }

    private function hideProgressVisibility(): void
    {
        $this->progressVisibility = false;
    }
}
