<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SynchroniseController extends AbstractController
{
    /**
     * @Route("/synchronise", name="synchronise_index")
     */
    public function index(): Response
    {
        return $this->render('synchronise/index.html.twig');
    }
}
