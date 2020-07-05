<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Section;
use App\Entity\Tag;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/dashboard/index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Blog')
        ;
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linktoUrl('Home', 'fa fa-home', '/'),
            MenuItem::linktoDashboard('Dashboard', 'fa fa-home'),

            MenuItem::section('Blog'),
            MenuItem::linkToCrud('Sections', 'fa fa-folder-open', Section::class),
            MenuItem::linkToCrud('Articles', 'fa fa-file-text', Article::class),
            MenuItem::linkToCrud('Tags', 'fa fa-tags', Tag::class),

            MenuItem::section('Users'),
            MenuItem::linkToCrud('Comments', 'fa fa-comment', Comment::class),
            MenuItem::linkToCrud('Users', 'fa fa-user', User::class),
        ];
        // yield MenuItem::linkToCrud('The Label', 'icon class', EntityClass::class);
    }

    public function configureAssets(): Assets
    {
        return parent::configureAssets()
            ->addJsFile('https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js')
        ;
    }
}
