<?php

namespace App\Repository;

use App\Entity\Article;
use App\Repository\Filter\ArticleFilter;
use App\Repository\Modifier\ArticleQueryModifier;
use App\Repository\RepoTrait\TranslatableTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    use TranslatableTrait;

    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, Article::class);
    }

    public function findArticleBySlug(
        string $slug,
        ?ArticleFIlter $filter = null,
        ?ArticleQueryModifier $modifier = null
    ): ?Article
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.slug = :slug')
            ->setParameter('slug', $slug)
        ;

        $this->applyModifier($qb, $modifier);

        return $this->applyHints($qb->getQuery(), $filter)->getOneOrNullResult();
    }

    public function findArticleById(
        string $id,
        ?ArticleFIlter $filter = null,
        ?ArticleQueryModifier $modifier = null
    ): ?Article
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.id = :id')
            ->setParameter('id', $id)
        ;

        $this->applyModifier($qb, $modifier);

        return $this->applyHints($qb->getQuery(), $filter)->getOneOrNullResult();
    }

    /**
     * @return Article[]
     */
    public function findArticles(?ArticleFilter $filter = null, ?ArticleQueryModifier $modifier = null): array
    {
        return $this->findArticlesQuery($filter, $modifier)->getResult();
    }

    public function countArticles(): int
    {
        return $this->count([]);
    }

    public function findArticlesQuery(?ArticleFilter $filter = null, ?ArticleQueryModifier $modifier = null): Query
    {
        $qb = $this->createQueryBuilder('a');

        $this->applyModifier($qb, $modifier);

        return $this->applyHints($qb->getQuery(), $filter);
    }

    public function findArticlesBySection(int $sectionId)
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.section = :section')
            ->setParameter('section', $sectionId)
        ;

        return $this->applyHints($qb->getQuery())->getResult();
    }

    private function applyModifier(QueryBuilder $qb, ?ArticleQueryModifier $modifier): void
    {
        if (!$modifier) {
            return;
        }
        if ($modifier->select) {
            $qb->select(
                array_map(static function ($field) {
                        return 'a.'.$field;
                    }, $modifier->select)
            );
        }
        if ($modifier->orderByField) {
            $qb->addOrderBy('a.'.$modifier->orderByField, $modifier->orderDirection ?: 'ASC');
        }
        if ($modifier->withSection) {
            $qb->addSelect('s')
                ->leftJoin('a.section', 's');
        }
        if ($modifier->withOwner) {
            $qb->addSelect('o')
                ->leftJoin('a.owner', 'o');
        }
        if ($modifier->withComments) {
            $qb->addSelect('c')
                ->leftJoin('a.comments', 'c');
        }

        if ($modifier->comments) {
            if ($modifier->comments->orderByField) {
                $qb->addOrderBy('c.'.$modifier->comments->orderByField, $modifier->comments->orderDirection ?: 'ASC');
            }
        }
    }

    private function applyHints(Query $query, ?ArticleFilter $filter = null): Query
    {
        $query = $this->applyTranslatables($query, $filter);

        $query->setHint('knp_paginator.count', $this->countArticles());

        return $query;
    }
}
