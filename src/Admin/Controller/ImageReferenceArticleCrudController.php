<?php

namespace App\Admin\Controller;

use App\Entity\ImageReferenceArticle;
use App\Admin\Form\ImageReferenceArticleType;
use App\Repository\ArticleRepository;
use App\Repository\ImageReferenceArticleRepository;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/image-reference-article")
 */
class ImageReferenceArticleCrudController extends AbstractController
{
    private $imageReferenceArticleRepository;
    private $articleRepository;
    private $imageRepository;
    private $entityManager;

    public function __construct(
        ImageReferenceArticleRepository $imageReferenceArticleRepository,
        ArticleRepository $articleRepository,
        ImageRepository $imageRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->imageReferenceArticleRepository = $imageReferenceArticleRepository;
        $this->articleRepository = $articleRepository;
        $this->imageRepository = $imageRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="admin-image-reference-article-index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('admin/crud/image-reference-article/index.html.twig', [
            'file_reference_articles' => $this->imageReferenceArticleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="admin_image_reference_article_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $articleId = $request->query->getInt('article_id');
        $imageId = $request->query->getInt('image_id');

        $imageReferenceArticle = new ImageReferenceArticle();
        if ($articleId) {
            $imageReferenceArticle->setArticle($this->articleRepository->findArticleById($articleId));
        }
        if ($imageId) {
            $imageReferenceArticle->setImage($this->imageRepository->findImageById($imageId));
        }

        $form = $this->createForm(ImageReferenceArticleType::class, $imageReferenceArticle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($imageReferenceArticle);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_article_edit', ['id' => $imageReferenceArticle->getArticle()->getId()]);
        }

        return $this->render('admin/crud/image-reference-article/new.html.twig', [
            'image_reference_article' => $imageReferenceArticle,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="admin-image-reference-article-edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ImageReferenceArticle $imageReferenceArticle): Response
    {
        $form = $this->createForm(ImageReferenceArticleType::class, $imageReferenceArticle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_article_edit', ['id' => $imageReferenceArticle->getArticle()->getId()]);
        }

        return $this->render('image_reference_article/edit.html.twig', [
            'image_reference_article' => $imageReferenceArticle,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="admin_image_reference_article_delete", methods={"POST"})
     */
    public function delete(Request $request, ImageReferenceArticle $imageReferenceArticle): Response
    {
        if ($this->isCsrfTokenValid('delete'.$imageReferenceArticle->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($imageReferenceArticle);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_article_edit', ['id' => $imageReferenceArticle->getArticle()->getId()]);
    }
}
