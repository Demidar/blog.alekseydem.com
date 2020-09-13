<?php

namespace App\Controller;

use App\Exception\NotFoundException;
use App\Service\Article\ArticleFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    private ArticleFactory $articleFactory;

    public function __construct(
        ArticleFactory $articleFactory
    ) {
        $this->articleFactory = $articleFactory;
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/article/{slug}", name="article")
     */
    public function article($slug): Response
    {
        try {
            $articlePage = $this->articleFactory->createPageBySlug($slug);
        } catch (NotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

        return $this->render('article/article.html.twig', [
            'article' => $articlePage->article,
            'attachedArticles' => $articlePage->attachedArticles,
            'comments' => $articlePage->comments,
            'breadcrumbs' => $articlePage->breadcrumbs,
            'createCommentForm' => $articlePage->createCommentFormView
        ]);
    }
}
