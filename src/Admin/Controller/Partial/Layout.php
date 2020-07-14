<?php

namespace App\Admin\Controller\Partial;

use App\Admin\Service\Menu\MenuBuilder;
use App\Entity\Article;
use App\Entity\Section;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class Layout extends AbstractController
{
    private $menuBuilder;

    public function __construct(MenuBuilder $menuBuilder)
    {
        $this->menuBuilder = $menuBuilder;
    }

    public function renderSide()
    {
        $menu = (new MenuBuilder())
            ->add(Section::class, MenuBuilder::TRANSLATABLE)
            ->add(Article::class, MenuBuilder::TRANSLATABLE)
        ;
        return $this->render('admin/partial/layout/side.html.twig');
    }
}
