<?php

namespace App\Controller;

use App\Article\Application\List\ListArticleQuery;
use App\Shared\Infrastructure\Bus\Query\QueryBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LogController extends AbstractController
{
    /**
     * @Route("/log", name="log_index")
     */
    public function index(): Response
    {
        return $this->render('log/index.html.twig');
    }

    /**
     * @Route("/log/article", name="log_article", methods={"GET"})
     */
    public function listAuthenticationOptions(
        QueryBus $queryBus,
    ): Response {
        $busRequest = new ListArticleQuery();
        $articles = $queryBus->dispatch($busRequest);
        $response = new JsonResponse($articles);
        return $response;
    }
}
