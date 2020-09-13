<?php

namespace App\Admin\Controller;

use App\Admin\Form\CommentType;
use App\Entity\Comment;
use App\Repository\CommentQuerying;
use App\Repository\Interfaces\CommentQueryingInterface;
use App\Repository\ModifierParams\CommentQueryModifierParams;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CommentCrudController extends AbstractCrudController
{
    private $paginator;
    private $commentRepository;
    private $entityManager;

    public function __construct(
        CommentQueryingInterface $commentRepository,
        PaginatorInterface $paginator,
        EntityManagerInterface $entityManager
    ) {
        $this->paginator = $paginator;
        $this->commentRepository = $commentRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/index", name="admin_comment_index")
     */
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        $commentsQuery = $this->commentRepository->findCommentsQuery(new CommentQueryModifierParams([
            'withOwner' => true,
            'withParent' => true,
            'withArticle' => true
        ]));

        $paginatedComments = $this->paginator->paginate(
            $commentsQuery,
            $page,
            20
        );

        return $this->render('admin/crud/comment/index.html.twig', [
            'paginatedComments' => $paginatedComments
        ]);
    }

    /**
     * @Route("/new", name="admin_comment_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $comment = new Comment();
        $comment->setOwner($this->getUser());

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_comment_index');
        }

        return $this->render('admin/crud/comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="admin_comment_edit", methods={"GET","POST"})
     */
    public function edit(int $id, Request $request): Response
    {
        $comment = $this->commentRepository->findCommentById($id, new CommentQueryModifierParams(['withOwner' => true]));
        if (!$comment) {
            throw new NotFoundHttpException('Comment not found');
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_comment_edit', ['id' => $id]);
        }

        return $this->render('admin/crud/comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="admin_comment_delete", methods={"POST"})
     */
    public function delete(Request $request, Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($comment);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_comment_index');
    }
}
