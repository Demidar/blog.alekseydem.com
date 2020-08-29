<?php

namespace App\Admin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="admin_dashboard")
     */
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard/dashboard.html.twig');
    }
}
