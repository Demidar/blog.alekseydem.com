<?php

namespace App\Admin\Controller;

use Symfony\Component\Routing\Annotation\Route;

class ArticleCrudController extends AbstractCrudController
{
    /**
     * @Route("/article/list", name="admin_article_list")
     */
    public function list()
    {
        return $this->render('admin/crud/article/list.html.twig');
    }
}
