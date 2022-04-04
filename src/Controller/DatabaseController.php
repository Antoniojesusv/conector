<?php

namespace App\Controller;

use App\Form\ConnectionType;
use App\Model\Database\DatabaseService;
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
    public function edit(Request $request, string $id, DatabaseService $databaseService): Response
    {
        $connectionEntity = $databaseService->getSqlCe();

        if ($id === 'mysqlServer') {
            $connectionEntity = $databaseService->getMysqlCe();
        }

        // if ($request->isMethod('POST')) {
        $form = $this->createForm(ConnectionType::class, $connectionEntity);
        $form->handleRequest($request);
        // }

        if ($form && $form->isSubmitted() && $form->isValid()) {
            $connectionEntity = $form->getData();
            $databaseService->persist($connectionEntity);
            return $this->redirectToRoute('database_index');
        }

        return $this->renderForm('database/edit.html.twig', [
            'form' => $form,
        ]);
    }
}
