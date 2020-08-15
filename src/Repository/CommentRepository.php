<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Repository\Filter\CommentFilter;
use App\Repository\Modifier\CommentQueryModifier;
use App\Repository\RepoTrait\TranslatableTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
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

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getClassMetadata(Comment::class));
    }

    public function findCommentById(int $id, ?CommentFilter $filter = null, ?CommentQueryModifier $modifier = null): ?Comment
    {
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
        ;

        $this->applyModifier($qb, $modifier);

        return $this->applyHints($qb->getQuery(), $filter)->getOneOrNullResult();
    }

    /**
     * @return Comment[]
     */
    public function findCommentsByArticle(int $id, ?CommentFilter $filter = null, ?CommentQueryModifier $modifier = null): array
    {
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.article = :article')
            ->setParameter('article', $id)
        ;

        $this->applyModifier($qb, $modifier);

        return $this->applyHints($qb->getQuery(), $filter)->getResult();
    }

    /**
     * @return Comment[]
     */
    public function findComments(?CommentFilter $filter = null, ?CommentQueryModifier $modifier = null): array
    {
        return $this->findCommentsQuery($filter, $modifier)->getResult();
    }

    public function countComments(): int
    {
        return $this->count([]);
    }

    public function findCommentsQuery(?CommentFilter $filter = null, ?CommentQueryModifier $modifier = null): Query
    {
        $qb = $this->createQueryBuilder('c');

        $this->applyModifier($qb, $modifier);

        return $this->applyHints($qb->getQuery(), $filter);
    }

    private function applyModifier(QueryBuilder $qb, ?CommentQueryModifier $modifier): void
    {
        if (!$modifier) {
            return;
        }
        if ($modifier->withOwner) {
            $qb->addSelect('o')
                ->leftJoin('c.owner', 'o');
        }
        if ($modifier->withParent) {
            $qb->addSelect('p')
                ->leftJoin('c.parent', 'p');
        }
        if ($modifier->withArticle) {
            $qb->addSelect('a')
                ->leftJoin('c.article', 'a');
        }
    }

    private function applyHints(Query $query, ?CommentFilter $filter = null): Query
    {
        /**
         * Althoug comments has no translations, these rules will be applied to such relations as Article.
         */
        $query = $this->applyTranslatables($query, $filter);

        $query->setHint('knp_paginator.count', $this->countComments());

        return $query;
    }
}
