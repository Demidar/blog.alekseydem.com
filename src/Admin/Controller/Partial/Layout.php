<?php

namespace App\Admin\Controller\Partial;

use App\Model\Link;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Layout extends AbstractController
{
    public function renderSide()
    {
        $menu = [
            new Link('Homepage', $this->generateUrl('homepage')),
            new Link('Section', $this->generateUrl('admin_section_index')),
            new Link('Article', $this->generateUrl('admin_article_index')),
            new Link('Comment', $this->generateUrl('admin_comment_index')),
            new Link('File', $this->generateUrl('admin_file_index')),
            new Link('Image', $this->generateUrl('admin_image_index'))
        ];
        return $this->render('admin/partial/layout/side.html.twig', [
            'menu' => $menu
        ]);
    }
}
