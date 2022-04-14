<?php

namespace App\Controller;

use App\Form\ServerModel;
use App\Form\ServerType;
use App\Model\Server\ServerService;
use App\Repository\ServerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServerController extends AbstractController
{
    /**
     * @Route("/server", name="server_index")
     */
    public function index(ServerRepository $serverRepository): Response
    {
        $serverEntity = $serverRepository->get();

        return $this->render('server/index.html.twig', [
            'server' => $serverEntity
        ]);
    }

    /**
     * @Route("/server/edit", name="server_post_edit", methods={"GET","POST"})
     */
    public function edit(
        Request $request,
        ServerRepository $serverRepository,
        ServerService $serverService
    ): Response {
        $serverModel = new ServerModel();

        if ($request->isMethod('GET')) {
            $serverEntity = $serverRepository->get();
            $serverModel->setData($serverEntity);
        }

        $form = $this->createForm(ServerType::class, $serverModel);
        $form->handleRequest($request);

        if ($form && $form->isSubmitted() && $form->isValid()) {
            $serverModel = $form->getData();
            $data = $serverModel->toArray();
            $serverService->persist($data);
            return $this->redirectToRoute('server_index');
        }

        return $this->renderForm('server/edit.html.twig', [
            'form' => $form,
        ]);
    }
}
