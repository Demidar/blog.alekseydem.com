<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentFormType;
use App\Repository\ArticleRepository;
use App\Repository\ModifierParams\ArticleQueryModifierParams;
use App\Repository\ModifierParams\CommentQueryModifierParams;
use App\Repository\ModifierParams\SectionQueryModifierParams;
use App\Service\Breadcrumbs;
use App\Service\HierarchyBuilder;
use DeepCopy\DeepCopy;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ArticleController extends AbstractController
{
    private $articleRepository;
    private $breadcrumbs;
    private $hierarchyBuilder;

    public function __construct(
        ArticleRepository $articleRepository,
        HierarchyBuilder $hierarchyBuilder,
        Breadcrumbs $breadcrumbs
    ) {
        $this->articleRepository = $articleRepository;
        $this->breadcrumbs = $breadcrumbs;
        $this->hierarchyBuilder = $hierarchyBuilder;
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/article/{slug}", name="article")
     */
    public function article($slug)
    {
        $article = $this->articleRepository->findArticleBySlug($slug, new ArticleQueryModifierParams([
            'withComments' => true,
            'comments' => new CommentQueryModifierParams([
                'orderByField'=> 'createdAt',
                'orderDirection' => 'DESC'
            ]),
            'withOwner' => true,
            'withImages' => true,
            'withSection' => true,
            'section' => new SectionQueryModifierParams([
                /*
                 * this relation is successfully joined, but erased in breadcrumbs forming.
                 * EntityManager will replace this join to a plain section without articles.
                 * So, I'll clone this below.
                 */
                'withArticles' => true,
                'articles' => new ArticleQueryModifierParams([
                    'findExceptSlugs' => [$slug],
                    'withImages' => true
                ])
            ])
        ]), 'a');
        if (!$article) {
            throw new NotFoundHttpException();
        }

        $clonedArticle = clone $article;

        $commentsHierarchy = $this->hierarchyBuilder->buildCommentsHierarchy($article->getComments());

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
