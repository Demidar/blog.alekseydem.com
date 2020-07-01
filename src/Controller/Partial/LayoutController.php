<?php

namespace App\Controller\Partial;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LayoutController extends AbstractController
{
    public function renderHeaderNav(): Response
    {
        return $this->render('partial/layout/_header-nav.html.twig');
    }
}
