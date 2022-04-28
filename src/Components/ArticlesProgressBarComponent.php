<?php

namespace App\Components;

use App\Repository\ArticleProductRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('articles_progress_bar')]
class ArticlesProgressBarComponent
{
    use DefaultActionTrait;

    #[LiveProp()]
    public int $currentPercentage = 0;

    private ArticleProductRepository $articleRepository;

    public function __construct(
        ArticleProductRepository $articleRepository
    ) {
        $this->articleRepository = $articleRepository;
    }

    public function mount()
    {
        $this->currentPercentage = $this->articleRepository->getCurrentPercentage();
    }
}
