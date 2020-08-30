<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentFormType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\JoinConditionParams\ArticleJoinConditionParams;
use App\Repository\ModifierParams\ArticleQueryModifierParams;
use App\Repository\ModifierParams\CommentQueryModifierParams;
use App\Repository\ModifierParams\SectionQueryModifierParams;
use App\Service\Breadcrumbs;
use App\Service\HierarchyBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ArticleController extends AbstractController
{
    private $articleRepository;
    private $breadcrumbs;
    private $hierarchyBuilder;
    private $commentRepository;

    public function __construct(
        ArticleRepository $articleRepository,
        CommentRepository $commentRepository,
        HierarchyBuilder $hierarchyBuilder,
        Breadcrumbs $breadcrumbs
    ) {
        $this->articleRepository = $articleRepository;
        $this->breadcrumbs = $breadcrumbs;
        $this->hierarchyBuilder = $hierarchyBuilder;
        $this->commentRepository = $commentRepository;
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/article/{slug}", name="article")
     */
    public function article($slug)
    {
        $article = $this->articleRepository->findArticleBySlug($slug, new ArticleQueryModifierParams([
            'withOwner' => true,
            'withImages' => true,
            'withSection' => true,
            'section' => new SectionQueryModifierParams([
                /*
                 * this relation is successfully joined, but erased in breadcrumbs forming.
                 * EntityManager will replace this join to a plain section without articles.
                 * So, I'll clone this below.
                 */
                'withArticles' => new ArticleJoinConditionParams([
                    'findExceptSlugs' => [$slug]
                ]),
                'articles' => new ArticleQueryModifierParams([
                    'withImages' => true
                ])
            ])
        ]));
        if (!$article) {
            throw new NotFoundHttpException();
        }

        $clonedArticle = clone $article;

        // the fetching of comments is separated for perfomance
        $comments = $this->commentRepository->findCommentsByArticle($article->getId(), new CommentQueryModifierParams([
            'orderByField'=> 'createdAt',
            'orderDirection' => 'DESC'
        ]));


        $commentsHierarchy = $this->hierarchyBuilder->buildCommentsHierarchy($comments);

        $breadcrumbs = $this->breadcrumbs->getBreadcrumbsForArticle($article);

        $emptyComment = (new Comment())->setArticle($article);

        $user = $this->getUser();
        if ($user instanceof UserInterface) {
            $createCommentFormView = $this->createForm(CommentFormType::class, $emptyComment, [
                'action' => $this->generateUrl('comment_submit'),
                'method' => 'POST'
            ])->createView();
        } else {
            $createCommentFormView = null;
        }

        return $this->render('article/article.html.twig', [
            'article' => $clonedArticle,
            'comments' => $commentsHierarchy,
            'breadcrumbs' => $breadcrumbs,
            'createCommentForm' => $createCommentFormView
        ]);
    }
}
