<?php

namespace App\Controller;

use App\Entity\FileReferenceArticle;
use App\Form\FileReferenceArticleType;
use App\Repository\FileReferenceArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/file/reference/article")
 */
class FileReferenceArticleController extends AbstractController
{
    /**
     * @Route("/", name="file_reference_article_index", methods={"GET"})
     */
    public function index(FileReferenceArticleRepository $fileReferenceArticleRepository): Response
    {
        return $this->render('file_reference_article/index.html.twig', [
            'file_reference_articles' => $fileReferenceArticleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="file_reference_article_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $fileReferenceArticle = new FileReferenceArticle();
        $form = $this->createForm(FileReferenceArticleType::class, $fileReferenceArticle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($fileReferenceArticle);
            $entityManager->flush();

            return $this->redirectToRoute('file_reference_article_index');
        }

        return $this->render('file_reference_article/new.html.twig', [
            'file_reference_article' => $fileReferenceArticle,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="file_reference_article_show", methods={"GET"})
     */
    public function show(FileReferenceArticle $fileReferenceArticle): Response
    {
        return $this->render('file_reference_article/show.html.twig', [
            'file_reference_article' => $fileReferenceArticle,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="file_reference_article_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, FileReferenceArticle $fileReferenceArticle): Response
    {
        $form = $this->createForm(FileReferenceArticleType::class, $fileReferenceArticle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('file_reference_article_index');
        }

        return $this->render('file_reference_article/edit.html.twig', [
            'file_reference_article' => $fileReferenceArticle,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="file_reference_article_delete", methods={"DELETE"})
     */
    public function delete(Request $request, FileReferenceArticle $fileReferenceArticle): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fileReferenceArticle->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($fileReferenceArticle);
            $entityManager->flush();
        }

        return $this->redirectToRoute('file_reference_article_index');
    }
}
