<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CreateCommentFormType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Service\Breadcrumbs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    private $articleRepository;
    private $breadcrumbs;
    private $commentRepository;

    public function __construct(
        ArticleRepository $articleRepository,
        CommentRepository $commentRepository,
        Breadcrumbs $breadcrumbs
    ) {
        $this->articleRepository = $articleRepository;
        $this->breadcrumbs = $breadcrumbs;
        $this->commentRepository = $commentRepository;
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/article/{slug}", name="article")
     */
    public function article($slug)
    {
        $article = $this->articleRepository->findPublicWithComments($slug);
        if (!$article) {
            throw new NotFoundHttpException();
        }

        $breadcrumbs = $this->breadcrumbs->getBreadcrumbsForArticle($article);

        $emptyComment = (new Comment())->setArticle($article);

        $createCommentForm = $this->createForm(CreateCommentFormType::class, $emptyComment);

        return $this->render('article/article.html.twig', [
            'article' => $article,
            'breadcrumbs' => $breadcrumbs,
            'createCommentForm' => $createCommentForm->createView()
        ]);
    }
}
