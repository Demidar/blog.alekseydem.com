<?php

namespace App\Admin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class EntityController extends AbstractController
{
    /**
     * @Route("/entity/{entity}/index", name="admin_entity_index")
     */
    public function index()
    {

    }
}
