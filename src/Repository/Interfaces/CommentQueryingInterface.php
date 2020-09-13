<?php

namespace App\Repository\Interfaces;


use App\Entity\Comment;
use App\Repository\ModifierParams\CommentQueryModifierParams;
use Doctrine\ORM\Query;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface CommentQueryingInterface
{
    public function findCommentById(int $id, ?CommentQueryModifierParams $modifier = null): ?Comment;

    /**
     * @return Comment[]
     */
    public function findCommentsByArticle(int $id, ?CommentQueryModifierParams $modifier = null): array;

    /**
     * @return Comment[]
     */
    public function findComments(?CommentQueryModifierParams $modifier = null): array;

    public function findCommentsQuery(?CommentQueryModifierParams $modifier = null): Query;
}
