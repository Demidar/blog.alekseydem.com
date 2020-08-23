<?php

namespace App\Admin\Controller;

use App\Admin\Form\ImageReferenceType;
use App\Entity\ImageReference;
use App\Repository\ImageReferenceRepository;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/image-reference")
 */
class ImageReferenceCrudController extends AbstractCrudController
{
    private $imageReferenceRepository;
    private $paginator;
    private $entityManager;
    private $uploaderHelper;

    public function __construct(
        ImageReferenceRepository $imageReferenceRepository,
        PaginatorInterface $paginator,
        EntityManagerInterface $entityManager,
        UploaderHelper $uploaderHelper
    ) {
        $this->imageReferenceRepository = $imageReferenceRepository;
        $this->paginator = $paginator;
        $this->entityManager = $entityManager;
        $this->uploaderHelper = $uploaderHelper;
    }

    /**
     * @Route("/new", name="admin_image-reference_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $imageReference = new ImageReference();

        $form = $this->createForm(ImageReferenceType::class, $imageReference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($imageReference);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_image-reference_index');
        }

        return $this->render('admin/crud/image-reference/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit/{id}", name="admin_image-reference_edit", methods={"GET", "POST"})
     */
    public function edit(int $id, Request $request): Response
    {
        $imageReference = $this->imageReferenceRepository->findImageReferenceById($id);
        if (!$imageReference) {
            throw new NotFoundHttpException('ImageReference not found');
        }

        $form = $this->createForm(ImageReferenceType::class, $imageReference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($imageReference);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_image-reference_index');
        }

        return $this->render('admin/crud/image-reference/edit.html.twig', [
            'imageReference' => $imageReference,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="admin_image-reference_delete", methods={"POST"})
     */
    public function delete(Request $request, ImageReference $imageReference): Response
    {
        if ($this->isCsrfTokenValid('delete'.$imageReference->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($imageReference);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_image-reference_index');
    }
}
