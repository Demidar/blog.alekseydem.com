<?php

namespace App\Admin\Controller;

use App\Admin\Form\FileType;
use App\Entity\File;
use App\Repository\FileRepository;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/file")
 */
class FileCrudController extends AbstractCrudController
{
    private $fileRepository;
    private $paginator;
    private $entityManager;
    private $uploaderHelper;

    public function __construct(
        FileRepository $fileRepository,
        PaginatorInterface $paginator,
        EntityManagerInterface $entityManager,
        UploaderHelper $uploaderHelper
    ) {
        $this->fileRepository = $fileRepository;
        $this->paginator = $paginator;
        $this->entityManager = $entityManager;
        $this->uploaderHelper = $uploaderHelper;
    }

    /**
     * @Route("/index", name="admin_file_index")
     */
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        $filesQuery = $this->fileRepository->findFilesQuery();

        $paginatedFiles = $this->paginator->paginate(
            $filesQuery,
            $page,
            20
        );

        return $this->render('admin/crud/file/index.html.twig', [
            'paginatedFiles' => $paginatedFiles
        ]);
    }

    /**
     * @Route("/new", name="admin_file_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $fileEntity = new File();
        $fileEntity->setOwner($this->getUser());

        $form = $this->createForm(FileType::class, $fileEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['file']->getData();
            $this->uploaderHelper->uploadFile($uploadedFile, $fileEntity);

            $this->entityManager->persist($fileEntity);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_file_index');
        }

        return $this->render('admin/crud/file/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Only database records are editable.
     *
     * @Route("/edit/{id}", name="admin_file_edit", methods={"GET", "POST"})
     */
    public function edit(int $id, Request $request): Response
    {
        $file = $this->fileRepository->findFileById($id);
        if (!$file) {
            throw new NotFoundHttpException('File not found');
        }

        $form = $this->createForm(FileType::class, $file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($file);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_file_index');
        }

        return $this->render('admin/crud/file/edit.html.twig', [
            'file' => $file,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="admin_file_delete", methods={"POST"})
     */
    public function delete(Request $request, File $file): Response
    {
        if ($this->isCsrfTokenValid('delete'.$file->getId(), $request->request->get('_token'))) {
            $this->uploaderHelper->deleteFile($file->getName());
            $this->entityManager->remove($file);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_file_index');
    }
}
