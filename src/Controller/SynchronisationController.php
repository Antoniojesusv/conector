<?php

namespace App\Controller;

use App\Article\Application\Synchronization\SynchronizationArticleQuery;
use App\Shared\Domain\Bus\Query\Contract\QueryBus;
use Hhxsv5\SSE\Event;
use Hhxsv5\SSE\SSE;
use Hhxsv5\SSE\StopSSEException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedJsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

class SynchronisationController extends AbstractController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {
        $this->queryBus = $queryBus;
    }

    /**
     * @Route("/synchronisation", name="synchronisation_index")
     */
    public function index(): Response
    {
        return $this->render('synchronisation/index.html.twig');
    }

    #[Route('/synchronize', name: 'app_synchronize', methods: "POST")]
    public function synchronize(Request $request): StreamedJsonResponse
    {
        $content = json_decode($request->getContent(), true);
        $rate = $content['rate'];
        $store = $content['store'];
        $company = $content['company'];
        $query = new SynchronizationArticleQuery($rate, $store, $company);

        try {
            return new StreamedJsonResponse(
                [
                    $this->queryBus->dispatch($query)
                ],
            );
        } catch (\Error $e) {
            return $this->json(['status' => 'error']);
        }
    }

    #[Route('/synchronize/sse', name: 'app_synchronizeSee')]
    public function synchronizeSee(): StreamedResponse
    {
        $response = new StreamedResponse();
        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('X-Accel-Buffering', 'no'); // Nginx: unbuffered responses suitable for Comet and HTTP streaming applications

        $callback = function () {
            $id = mt_rand(1, 1000);

            $stream = fopen('php://temp/synchronization', 'r+');

            rewind($stream);

            // read the data from the stream
            $content = stream_get_contents($stream); // Hello, this is a stream in memory or a 

            $news = [['id' => $id, 'percentage' => $content]]; // Get news from database or service.

            if (empty($news)) {
                return false; // Return false if no new messages
            }

            // Stop if something happens or to clear connection, browser will retry
            if ($this->shouldStop($status = 'synchronize')) {
                throw new StopSSEException();
            }

            return json_encode(compact('news'));
            // return ['event' => 'ping', 'data' => 'ping data']; // Custom event temporarily: send ping event
            // return ['id' => uniqid(), 'data' => json_encode(compact('news'))]; // Custom event Id
        };

        $response->setCallback(fn() => (new SSE(new Event($callback, 'news')))->start());

        return $response;
    }

    private function shouldStop(string $status): bool
    {
        return $status === 'end';
    }
}
