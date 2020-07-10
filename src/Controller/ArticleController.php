<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Service\Breadcrumbs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    private $articleRepository;
    private $breadcrumbs;

    public function __construct(
        ArticleRepository $articleRepository,
        Breadcrumbs $breadcrumbs
    ) {
        $this->articleRepository = $articleRepository;
        $this->breadcrumbs = $breadcrumbs;
    }

    /**
     * @Route("/article/{slug}", name="article")
     */
    public function article($slug)
    {
        $article = $this->articleRepository->findPublic($slug);
        if (!$article) {
            throw new NotFoundHttpException();
        }

        $breadcrumbs = $this->breadcrumbs->getBreadcrumbsForArticle($article);

        return $this->render('article/article.html.twig', [
            'article' => $article,
            'breadcrumbs' => $breadcrumbs
        ]);
    }
}
