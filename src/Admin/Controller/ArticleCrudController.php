<?php

namespace App\Admin\Controller;

use App\Admin\Form\ArticleType;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\Filter\ArticleFilter;
use App\Repository\Modifier\ArticleQueryModifier;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/article")
 */
class ArticleCrudController extends AbstractCrudController
{
    private $articleRepository;
    private $paginator;

    public function __construct(
        ArticleRepository $articleRepository,
        PaginatorInterface $paginator
    ) {
        $this->articleRepository = $articleRepository;
        $this->paginator = $paginator;
    }

    /**
     * @Route("/index", name="admin_article_index")
     */
    public function index(Request $request): Response
    {
        $lang = $request->query->get('lang', $request->getLocale());
        $page = $request->query->getInt('page', 1);

        $articlesQuery = $this->articleRepository->findArticlesQuery(new ArticleFilter([
            'locale' => $lang,
            'fallback' => true
        ]), new ArticleQueryModifier([
            'withSection' => true,
            'withOwner' => true
        ]));

        $paginatedArticles = $this->paginator->paginate(
            $articlesQuery,
            $page,
            20
        );

        return $this->render('admin/crud/article/index.html.twig', [
            'paginatedArticles' => $paginatedArticles
        ]);
    }

    /**
     * @Route("/new", name="admin_article_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $lang = $request->query->get('lang', $request->getLocale());

        $article = new Article();
        $article->setOwner($this->getUser());
        $article->setTranslatableLocale($lang);

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('admin_article_index');
        }

        return $this->render('admin/crud/article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="admin_article_edit", methods={"GET","POST"})
     */
    public function edit(int $id, Request $request): Response
    {
        $lang = $request->query->get('lang', $request->getLocale());

        $article = $this->articleRepository->findArticleById($id, new ArticleFilter(['locale' => $lang, 'fallback' => false]));
        if (!$article) {
            throw new NotFoundHttpException('Article not found');
        }
        $article->setTranslatableLocale($lang);

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_article_edit', ['id' => $id]);
        }

        return $this->render('admin/crud/article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="admin_article_delete", methods={"POST"})
     */
    public function delete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_article_index');
    }
}
