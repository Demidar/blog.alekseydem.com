<?php

namespace App\Form\Transformer;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class CommentToIdTransformer implements DataTransformerInterface
{
    private $commentRepository;

    public function __construct(
        CommentRepository $commentRepository
    ) {
        $this->commentRepository = $commentRepository;
    }

    /**
     * @param Comment $comment
     * @return int|null
     */
    public function transform($comment): ?int
    {
        if (!$comment) {
            return null;
        }

        return $comment->getId();
    }

    public function reverseTransform($id): ?Comment
    {
        if (!$id) {
            return null;
        }

        $comment = $this->commentRepository->findCommentById($id);

        if (!$comment) {
            throw new TransformationFailedException('This comment does not exist!');
        }

        return $comment;
    }
}
