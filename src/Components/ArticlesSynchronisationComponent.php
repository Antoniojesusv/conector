<?php

namespace App\Components;

use App\Model\Synchronisation\ArticlesSynchronisationService;
use App\Repository\ArticleProductRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Error;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

#[AsLiveComponent('articles_synchronisation', csrf: false)]
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

    #[LiveProp()]
    public string $shopStore = '';

    #[LiveProp()]
    public string $totalArticles = '';

    #[LiveProp()]
    public string $synchronisedArticles = '';

    private ArticlesSynchronisationService $synchronisationService;
    private ArticleProductRepository $articleRepository;
    private ContainerBagInterface $params;

    public function __construct(
        ArticlesSynchronisationService $synchronisationService,
        ArticleProductRepository $articleRepository,
        ContainerBagInterface $params
    ) {
        $this->synchronisationService = $synchronisationService;
        $this->articleRepository = $articleRepository;
        $this->params = $params;
    }

    public function mount()
    {
        $this->shopStore = $this->params->get('shop.store');
        $this->totalArticles = $this->articleRepository->getTotalArticles();
        $this->synchronisedArticles = $this->articleRepository->getSynchronisationArticles();
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
            $this->shopStore = $this->params->get('shop.store');
            $this->totalArticles = $this->articleRepository->getTotalArticles();
            $this->synchronisedArticles = $this->articleRepository->getSynchronisationArticles();
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
