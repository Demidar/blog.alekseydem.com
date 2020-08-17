<?php

namespace App\Admin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/upload")
 */
class UploadController extends AbstractController
{
    /**
     * @Route("/article/images", name="upload-article-images")
     */
    public function articleImages()
    {

    }
}
