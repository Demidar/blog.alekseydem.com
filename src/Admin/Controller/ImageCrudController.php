<?php

namespace App\Admin\Controller;

use App\Admin\Form\ImageType;
use App\Entity\Image;
use App\Repository\ImageRepository;
use App\Repository\Interfaces\ImageQueryingInterface;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/image")
 */
class ImageCrudController extends AbstractCrudController
{
    private $imageRepository;
    private $paginator;
    private $entityManager;
    private $uploaderHelper;

    public function __construct(
        ImageQueryingInterface $imageRepository,
        PaginatorInterface $paginator,
        EntityManagerInterface $entityManager,
        UploaderHelper $uploaderHelper
    ) {
        $this->imageRepository = $imageRepository;
        $this->paginator = $paginator;
        $this->entityManager = $entityManager;
        $this->uploaderHelper = $uploaderHelper;
    }

    /**
     * @Route("/index", name="admin_image_index")
     */
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        $imagesQuery = $this->imageRepository->findImagesQuery();

        $paginatedImages = $this->paginator->paginate(
            $imagesQuery,
            $page,
            20
        );

        return $this->render('admin/crud/image/index.html.twig', [
            'paginatedImages' => $paginatedImages
        ]);
    }

    /**
     * @Route("/new", name="admin_image_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $image = new Image();
        $image->setOwner($this->getUser());

        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedImage */
            $uploadedImage = $form['image']->getData();
            $this->uploaderHelper->uploadFile($uploadedImage, $image);
            $this->entityManager->persist($image);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_image_index');
        }

        return $this->render('admin/crud/image/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit/{id}", name="admin_image_edit", methods={"GET", "POST"})
     */
    public function edit(int $id, Request $request): Response
    {
        $image = $this->imageRepository->findImageById($id);
        if (!$image) {
            throw new NotFoundHttpException('Image not found');
        }

        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($image);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_image_index');
        }

        return $this->render('admin/crud/image/edit.html.twig', [
            'image' => $image,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="admin_image_delete", methods={"POST"})
     */
    public function delete(Request $request, Image $image): Response
    {
        if ($this->isCsrfTokenValid('delete'.$image->getId(), $request->request->get('_token'))) {
            $this->uploaderHelper->deleteFile($image->getName());
            $this->entityManager->remove($image);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_image_index');
    }
}
