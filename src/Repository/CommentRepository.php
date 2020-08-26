<?php

namespace App\Repository;

use App\Entity\Comment;
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

    public function findCommentById(int $id, ?CommentQueryModifier $modifier = null): ?Comment
    {
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
        ;

        $this->applyModifier($qb, $modifier);

        return $this->applyHints($qb->getQuery(), $modifier)->getOneOrNullResult();
    }

    /**
     * @return Comment[]
     */
    public function findCommentsByArticle(int $id, ?CommentQueryModifier $modifier = null): array
    {
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.article = :article')
            ->setParameter('article', $id)
        ;

        $this->applyModifier($qb, $modifier);

        return $this->applyHints($qb->getQuery(), $modifier)->getResult();
    }

    /**
     * @return Comment[]
     */
    public function findComments(?CommentQueryModifier $modifier = null): array
    {
        return $this->findCommentsQuery($modifier)->getResult();
    }

    public function countComments(): int
    {
        return $this->count([]);
    }

    public function findCommentsQuery(?CommentQueryModifier $modifier = null): Query
    {
        $qb = $this->createQueryBuilder('c');

        $this->applyModifier($qb, $modifier);

        return $this->applyHints($qb->getQuery(), $modifier);
    }

    public function applyModifier(QueryBuilder $qb, ?CommentQueryModifier $modifier, string $alias = 'c'): void
    {
        if (!$modifier) {
            return;
        }
        if ($modifier->withOwner) {
            $qb->addSelect('c_owner')
                ->leftJoin("$alias.owner", 'c_owner');
        }
        if ($modifier->withParent) {
            $qb->addSelect('c_parent')
                ->leftJoin("$alias.parent", 'c_parent');
        }
        if ($modifier->withArticle) {
            $qb->addSelect('c_article')
                ->leftJoin("$alias.article", 'c_article');
        }
        if ($modifier->orderByField) {
            $qb->addOrderBy("$alias.{$modifier->orderByField}", $modifier->orderDirection ?: 'ASC');
        }
    }

    private function applyHints(Query $query, ?CommentQueryModifier $modifier = null): Query
    {
        /**
         * Althoug comments has no translations, these rules will be applied to such relations as Article.
         */
        $query = $this->applyTranslatables($query, $modifier);

        $query->setHint('knp_paginator.count', $this->countComments());

        return $query;
    }
}
