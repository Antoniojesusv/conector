<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SynchronisationController extends AbstractController
{
    /**
     * @Route("/synchronisation", name="synchronisation_index")
     */
    public function index(): Response
    {
        return $this->render('synchronisation/index.html.twig');
    }
}
