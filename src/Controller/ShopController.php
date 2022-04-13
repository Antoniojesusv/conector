<?php

namespace App\Controller;

use App\Form\ShopModel;
use App\Form\ShopType;
use App\Model\Shop\ShopService;
use App\Repository\ShopRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    /**
     * @Route("/shop", name="shop_index")
     */
    public function index(ShopRepository $shopRepository): Response
    {
        $shopEntity = $shopRepository->get();

        return $this->render('shop/index.html.twig', [
            'shop' => $shopEntity
        ]);
    }

    /**
     * @Route("/shop/edit", name="shop_post_edit", methods={"GET","POST"})
     */
    public function edit(
        Request $request,
        ShopRepository $shopRepository,
        ShopService $shopService
    ): Response {
        $shopModel = new ShopModel();

        if ($request->isMethod('GET')) {
            $shopEntity = $shopRepository->get();
            $shopModel->setData($shopEntity);
        }

        $form = $this->createForm(ShopType::class, $shopModel);
        $form->handleRequest($request);

        if ($form && $form->isSubmitted() && $form->isValid()) {
            $shopModel = $form->getData();
            $data = $shopModel->toArray();
            $shopService->persist($data);
            return $this->redirectToRoute('shop_index');
        }

        return $this->renderForm('shop/edit.html.twig', [
            'form' => $form,
        ]);
    }
}
