<?php

namespace App\Service\Article;

use App\Entity\Comment;
use App\Exception\NotFoundException;
use App\Form\CommentFormType;
use App\Model\ArticlePage;
use App\Repository\Interfaces\ArticleSourceInterface;
use App\Repository\Interfaces\CommentSourceInterface;
use App\Repository\ModifierParams\ArticleQueryModifierParams;
use App\Repository\ModifierParams\CommentQueryModifierParams;
use App\Service\Breadcrumbs;
use App\Service\HierarchyBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ArticleFactory
{
    private ArticleSourceInterface $articleRepository;
    private CommentSourceInterface $commentRepository;
    private Security $security;
    private FormFactoryInterface $formFactory;
    private UrlGeneratorInterface $urlGenerator;
    private HierarchyBuilder $hierarchyBuilder;
    private Breadcrumbs $breadcrumbs;
    private TranslatorInterface $translator;

    public function __construct(
        ArticleSourceInterface $articleRepository,
        CommentSourceInterface $commentRepository,
        Security $security,
        FormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator,
        HierarchyBuilder $hierarchyBuilder,
        Breadcrumbs $breadcrumbs,
        TranslatorInterface $translator
    ) {
        $this->articleRepository = $articleRepository;
        $this->commentRepository = $commentRepository;
        $this->security = $security;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
        $this->hierarchyBuilder = $hierarchyBuilder;
        $this->breadcrumbs = $breadcrumbs;
        $this->translator = $translator;
    }

    /**
     * @throws NotFoundException
     */
    public function createPageBySlug(string $slug): ArticlePage
    {
        $article = $this->articleRepository->findArticleBySlug($slug, new ArticleQueryModifierParams([
            'withOwner' => true,
            'withImages' => true,
            'withSection' => true,
        ]));
        if (!$article) {
            throw new NotFoundException(sprintf($this->translator->trans('article.slug-not-found', [
                'slug' => $slug
            ])));
        }

        $attachedArticles = $this->articleRepository->findArticlesBySection(
            $article->getSection()->getId(),
            new ArticleQueryModifierParams([
                'findExceptSlugs' => [$slug],
                'withImages' => true
            ])
        );

        $comments = $this->commentRepository->findCommentsByArticle($article->getId(), new CommentQueryModifierParams([
            'orderByField'=> 'createdAt',
            'orderDirection' => 'DESC'
        ]));

        $commentsHierarchy = $this->hierarchyBuilder->buildCommentsHierarchy($comments);

        $breadcrumbs = $this->breadcrumbs->getBreadcrumbsForArticle($article);

        $emptyComment = (new Comment())->setArticle($article);

        $user = $this->security->getUser();
        if ($user instanceof UserInterface) {
            $createCommentFormView = $this->formFactory->create(CommentFormType::class, $emptyComment, [
                'action' => $this->urlGenerator->generate('comment_submit'),
                'method' => 'POST'
            ])->createView();
        } else {
            $createCommentFormView = null;
        }

        $model = new ArticlePage();
        $model->article = $article;
        $model->attachedArticles = $attachedArticles;
        $model->comments = $commentsHierarchy;
        $model->breadcrumbs = $breadcrumbs;
        $model->createCommentFormView = $createCommentFormView;

        return $model;
    }
}
