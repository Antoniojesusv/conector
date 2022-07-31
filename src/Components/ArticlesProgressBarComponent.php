<?php

namespace App\Components;

use App\Repository\ArticleProductRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('articles_progress_bar')]
class ArticlesProgressBarComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public float $currentPercentage = 0;

    private ArticleProductRepository $articleRepository;
    private ContainerBagInterface $params;

    public function __construct(
        ArticleProductRepository $articleRepository,
        ContainerBagInterface $params
    ) {
        $this->articleRepository = $articleRepository;
        $this->params = $params;
        // $this->currentPercentage = $this->articleRepository->getCurrentPercentage();
    }

    public function getProgress(): float
    {
        return round($this->params->get('articles.progress.update'));
    }

    public function mount()
    {
        $this->currentPercentage = $this->articleRepository->getCurrentPercentage();
    }
}
