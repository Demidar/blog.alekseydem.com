<?php

namespace App\Admin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/browse")
 */
class BrowserController extends AbstractController
{
    /**
     * @Route("/images", name="browse-images")
     */
    public function images(Request $request): Response
    {
        return $this->render('admin/browser/images.html.twig');
    }
}
