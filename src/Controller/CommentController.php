<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentFormType;
use App\Repository\Interfaces\CommentSourceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/comment")
 */
class CommentController extends AbstractController
{
    private $entityManager;
    private $translator;
    private $commentRepository;

    public function __construct(
        CommentSourceInterface $commentRepository,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator
    ) {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->commentRepository = $commentRepository;
    }

    /**
     * @Route("/submit", name="comment_submit", methods={"POST"})
     */
    public function submit(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user instanceof UserInterface) {
            throw new \LogicException("Current user does not supported.");
        }

        $comment = new Comment();
        $comment->setOwner($user);

        if ($this->handleForm($comment, $request)) {
            $this->addFlash('message', $this->translator->trans('flash.comment-submitted-successfully'));
        } else {
            $this->addFlash('error', $this->translator->trans('flash.comment-does-not-submitted'));
        }

        return $this->redirectToRoute('article', ['slug' => $comment->getArticle()->getSlug()]);
    }

    /**
     * @Route("/edit/{id}", name="comment_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, int $id): Response
    {
        $user = $this->getUser();
        if (!$user instanceof UserInterface) {
            throw new \LogicException("Current user does not supported.");
        }

        $comment = $this->commentRepository->findCommentById($id);
        if (!$comment) {
            throw new NotFoundHttpException('Comment not found or you have no access.');
        }

        if ($this->handleForm($comment, $request)) {
            $this->addFlash('message', $this->translator->trans('flash.comment-updated-successfully'));
        } else {
            $this->addFlash('error', $this->translator->trans('flash.comment-does-not-updated'));
        }

        return $this->redirectToRoute('article', ['slug' => $comment->getArticle()->getSlug()]);
    }

    private function handleForm($comment, $request)
    {
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            return true;
        }
        return false;
    }
}
