<?php

namespace App\Components;

use App\Repository\ArticleRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('progress_bar')]
class ProgressBarComponent
{
    use DefaultActionTrait;

    #[LiveProp()]
    public int $currentPercentage = 0;

    private ArticleRepository $articleRepository;

    public function __construct(
        ArticleRepository $articleRepository
    ) {
        $this->articleRepository = $articleRepository;
    }

    public function mount()
    {
        $this->currentPercentage = $this->articleRepository->getCurrentPercentage();
    }
}
