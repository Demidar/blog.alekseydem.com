<?php

namespace App\Admin\Controller\Partial;

use App\Admin\Service\Menu\MenuBuilder;
use App\Entity\Article;
use App\Entity\Section;
use App\Model\Link;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class Layout extends AbstractController
{
    public function renderSide()
    {
        $menu = [
            new Link('Homepage', $this->generateUrl('homepage')),
            new Link('Section', $this->generateUrl('admin_section_index')),
            new Link('Article', $this->generateUrl('admin_article_index')),
            new Link('Comment', $this->generateUrl('admin_comment_index')),
        ];
        return $this->render('admin/partial/layout/side.html.twig', [
            'menu' => $menu
        ]);
    }
}
