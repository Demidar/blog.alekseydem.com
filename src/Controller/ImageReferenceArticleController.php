<?php

namespace App\Controller;

use App\Entity\ImageReferenceArticle;
use App\Form\ImageReferenceArticleType;
use App\Repository\ImageReferenceArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/image/reference/article")
 */
class ImageReferenceArticleController extends AbstractController
{
    /**
     * @Route("/", name="image_reference_article_index", methods={"GET"})
     */
    public function index(ImageReferenceArticleRepository $imageReferenceArticleRepository): Response
    {
        return $this->render('image_reference_article/index.html.twig', [
            'image_reference_articles' => $imageReferenceArticleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="image_reference_article_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $imageReferenceArticle = new ImageReferenceArticle();
        $form = $this->createForm(ImageReferenceArticleType::class, $imageReferenceArticle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($imageReferenceArticle);
            $entityManager->flush();

            return $this->redirectToRoute('image_reference_article_index');
        }

        return $this->render('image_reference_article/new.html.twig', [
            'image_reference_article' => $imageReferenceArticle,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="image_reference_article_show", methods={"GET"})
     */
    public function show(ImageReferenceArticle $imageReferenceArticle): Response
    {
        return $this->render('image_reference_article/show.html.twig', [
            'image_reference_article' => $imageReferenceArticle,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="image_reference_article_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ImageReferenceArticle $imageReferenceArticle): Response
    {
        $form = $this->createForm(ImageReferenceArticleType::class, $imageReferenceArticle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('image_reference_article_index');
        }

        return $this->render('image_reference_article/edit.html.twig', [
            'image_reference_article' => $imageReferenceArticle,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="image_reference_article_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ImageReferenceArticle $imageReferenceArticle): Response
    {
        if ($this->isCsrfTokenValid('delete'.$imageReferenceArticle->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($imageReferenceArticle);
            $entityManager->flush();
        }

        return $this->redirectToRoute('image_reference_article_index');
    }
}
