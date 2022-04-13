<?php

namespace App\Controller;

use App\Form\NmConnectionModel;
use App\Form\NmConnectionType;
use App\Form\UwConnectionModel;
use App\Form\UwConnectionType;
use App\Model\Database\DatabaseService;
use App\Repository\NmConnectionRepository;
use App\Repository\UwConnectionRepository;
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
        NmConnectionRepository $nmConnectionRepository,
        UwConnectionRepository $uWconnectionRepository
    ): Response {
        $connectionModel = new UwConnectionModel();
        $uWconnectionEntity = $uWconnectionRepository->get();
        $connectionModel->setData($uWconnectionEntity);
        $form = $this->createForm(UwConnectionType::class, $connectionModel);

        if ($id === 'mysqlServer') {
            $connectionModel = new NmConnectionModel();
            $nmMysqlConnectionEntity = $nmConnectionRepository->getByType('mysqlServer');
            $connectionModel->setData($nmMysqlConnectionEntity);
            $form = $this->createForm(NmConnectionType::class, $connectionModel, [
                'type' => $id
            ]);
        }

        if ($id === 'nmSqlServer') {
            $connectionModel = new NmConnectionModel();
            $nmSqlConnectionEntity = $nmConnectionRepository->getByType('sqlServer');
            $connectionModel->setData($nmSqlConnectionEntity);
            $form = $this->createForm(NmConnectionType::class, $connectionModel, [
                'type' => $id
            ]);
        }

        $form->handleRequest($request);
        
        if ($form && $form->isSubmitted() && $form->isValid()) {
            $connectionModel = $form->getData();
            $data = $connectionModel->toArray();
            $authentication = $form->get('authentication')->getData();
            $databaseService->persist($data, $authentication);
            return $this->redirectToRoute('database_index');
        }

        if ($id === 'mysqlServer' | $id === 'nmSqlServer') {
            return $this->renderForm('database/edit.html.twig', [
                'form' => $form,
            ]);
        }

        return $this->renderForm('database/uw_edit.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/database/options", name="database_authentication_options", methods={"GET"})
     */
    public function listAuthenticationOptions()
    {
        return $this->render('database/authentication_options.html.twig');
    }
}
