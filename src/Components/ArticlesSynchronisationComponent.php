<?php
namespace App\Components;

use App\Synchronisation\Application\Update\ArticleRequest;
use App\Synchronisation\Application\Update\ArticleSynchronisationService;
use App\Model\Synchronisation\ArticlesSynchronisationService;
use App\Repository\ArticleProductRepository;
use App\Shared\Domain\Bus\Command\CommandBus;
use App\Synchronisation\Application\Update\ArticleSynchronisation;
use App\Synchronisation\Application\Update\UpdateArticleCommand;
use App\Synchronisation\Application\Update\UpdateArticleCommandHandler;
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
    public string $rate = '';

    #[LiveProp()]
    public string $shopStore = '';

    #[LiveProp()]
    public string $totalArticles = '';

    #[LiveProp()]
    public string $synchronisedArticles = '';

    public function __construct(
        private ArticlesSynchronisationService $synchronisationService,
        // private ArticleSynchronisationService $articleSynchronisationService,
        private ArticleSynchronisation $articleSynchronisation,
        private ArticleProductRepository $articleRepository,
        private CommandBus $commandBusSync,
        // private CommandHandler $updateArticleCommandHandler,
        private ContainerBagInterface $params
    ) {
        $this->synchronisationService = $synchronisationService;
        // $this->articleSynchronisationService = $articleSynchronisationService;
        $this->articleRepository = $articleRepository;
        $this->params = $params;
    }

    public function mount()
    {
        $this->rate = $this->params->get('shop.rate');
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
            // $this->synchronisationService->synchronise();
            $rate = $this->params->get('shop.rate');
            $store = $this->params->get('shop.store');
            $company = '01';
            $command = new UpdateArticleCommand($rate, $store, $company);
            $this->commandBusSync->register($command, new UpdateArticleCommandHandler($this->articleSynchronisation));
            // $articleRequest = new UpdateArticleCommand($rate, $store, $company);
            // $this->articleSynchronisationService->__invoke($articleRequest);
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

    #[LiveAction]
    public function reset(): void
    {
        $this->articleRepository->resetCurrentProgress();
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
