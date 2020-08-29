<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Repository\Modifier\CommentQueryModifier;
use App\Repository\ModifierParams\CommentQueryModifierParams;
use App\Repository\RepoTrait\TranslatableTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Gedmo\Tree\Entity\Repository\ClosureTreeRepository;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ClosureTreeRepository
{
    use TranslatableTrait;

    private CommentQueryModifier $queryModifier;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getClassMetadata(Comment::class));
    }

    /**
     * @required
     */
    public function setQueryModifier(CommentQueryModifier $queryModifier)
    {
        $this->queryModifier = $queryModifier;
    }

    public function findCommentById(int $id, ?CommentQueryModifierParams $modifier = null): ?Comment
    {
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
        ;

        $this->queryModifier->applyModifier($qb, $modifier, 'c');

        return $this->applyHints($qb->getQuery(), $modifier)->getOneOrNullResult();
    }

    /**
     * @return Comment[]
     */
    public function findCommentsByArticle(int $id, ?CommentQueryModifierParams $modifier = null): array
    {
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.article = :article')
            ->setParameter('article', $id)
        ;

        $this->queryModifier->applyModifier($qb, $modifier, 'c');

        return $this->applyHints($qb->getQuery(), $modifier)->getResult();
    }

    /**
     * @return Comment[]
     */
    public function findComments(?CommentQueryModifierParams $modifier = null): array
    {
        return $this->findCommentsQuery($modifier)->getResult();
    }

    public function countComments(): int
    {
        return $this->count([]);
    }

    public function findCommentsQuery(?CommentQueryModifierParams $modifier = null): Query
    {
        $qb = $this->createQueryBuilder('c');

        $this->queryModifier->applyModifier($qb, $modifier, 'c');

        return $this->applyHints($qb->getQuery(), $modifier);
    }

    private function applyHints(Query $query, ?CommentQueryModifierParams $modifier = null): Query
    {
        /**
         * Althoug comments has no translations, these rules will be applied to such relations as Article.
         */
        $query = $this->applyTranslatables($query, $modifier);

        $query->setHint('knp_paginator.count', $this->countComments());

        return $query;
    }
}
