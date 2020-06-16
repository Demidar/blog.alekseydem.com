<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LayoutController extends AbstractController
{
    public function renderHeaderNav()
    {
        return $this->render('layout/_header-nav.html.twig');
    }
}
