<?php

namespace App\Controller;

use App\Connection\Application\Edit\EditConnectionCommand;
use App\Connection\Application\Find\FindConnectionQuery;
use App\Form\NmConnectionModel;
use App\Form\NmConnectionType;
use App\Model\Database\DatabaseService;
use App\Repository\NmConnectionRepository;
use App\Repository\UwConnectionRepository;
use App\Shared\Infrastructure\Bus\Command\CommandBus;
use App\Shared\Infrastructure\Bus\Query\QueryBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DatabaseController extends AbstractController
{

    /**
     * @Route("/", name="database_index")
     */
    public function index(): Response
    {
        return $this->render('database/index.html.twig');
    }

    /**
     * @Route("/database/{id}/edit", name="database_post_edit")
     */
    public function edit(
        Request $request,
        string $id,
        DatabaseService $databaseService,
        // NmConnectionRepository $nmConnectionRepository,
        // UwConnectionRepository $uWconnectionRepository,
        QueryBus $queryBus,
        CommandBus $commandBus,
    ): Response {
        // $connectionModel = new UwConnectionModel();
        // $uWconnectionEntity = $uWconnectionRepository->get();
        // $connectionModel->setData($uWconnectionEntity);
        // $form = $this->createForm(UwConnectionType::class, $connectionModel);

        $busRequest = new FindConnectionQuery($id);
        $connection = $queryBus->dispatch($busRequest);

        if ($id === 'mysqlServer') {
            $busRequest = new FindConnectionQuery($id);
            $connection = $queryBus->dispatch($busRequest);
        }

        $connectionModel = new NmConnectionModel();
        $connectionModel->setData($connection);
        $form = $this->createForm(NmConnectionType::class, $connectionModel, [
            'type' => $id
        ]);

        // if ($id === 'sqlServer') {
        //     $connectionModel = new NmConnectionModel();
        //     $nmSqlConnectionEntity = $nmConnectionRepository->getByType('sqlServer');
        //     $connectionModel->setData($nmSqlConnectionEntity);
        //     $form = $this->createForm(NmConnectionType::class, $connectionModel, [
        //         'type' => $id
        //     ]);
        // }

        $form->handleRequest($request);

        if ($form && $form->isSubmitted() && $form->isValid()) {
            $connectionModel = $form->getData();
            $data = $connectionModel->toArray();

            $authentication = $form->get('authentication')->getData();

            $data['type'] = $authentication;
            $data['databaseName'] = $data['database'];
            $data['port'] = (int) $data['exposedPort'];

            unset($data['database']);
            unset($data['exposedPort']);

            if ($authentication !== 'mysqlServer') {
                $data['type'] = 'sqlServer';
            }

            $command = EditConnectionCommand::createFromData(...$data);
            $commandBus->dispatch($command);
            // $databaseService->persist($data, $authentication);
            return $this->redirectToRoute('database_index');
        }

        if ($id === 'mysqlServer' | $id === 'sqlServer') {
            return $this->renderForm('database/edit.html.twig', [
                'form' => $form,
            ]);
        }

        // return $this->renderForm('database/uw_edit.html.twig', [
        //     'form' => $form,
        // ]);
    }

    /**
     * @Route("/database/options", name="database_authentication_options", methods={"GET"})
     */
    // public function listAuthenticationOptions()
    // {
    //     return $this->render('database/authentication_options.html.twig');
    // }
}
