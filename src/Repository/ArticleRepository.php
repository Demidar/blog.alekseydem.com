<?php

namespace App\Repository;

use App\Entity\Article;
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

    private $commentRepository;

    public function __construct(
        ManagerRegistry $registry,
        CommentRepository $commentRepository
    ) {
        parent::__construct($registry, Article::class);
        $this->commentRepository = $commentRepository;
    }

    public function findArticleBySlug(string $slug, ?ArticleQueryModifier $modifier = null): ?Article
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.slug = :slug')
            ->setParameter('slug', $slug)
        ;

        $this->applyModifier($qb, $modifier);

        return $this->applyHints($qb->getQuery(), $modifier)->getOneOrNullResult();
    }

    public function findArticleById(string $id, ?ArticleQueryModifier $modifier = null): ?Article
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.id = :id')
            ->setParameter('id', $id)
        ;

        $this->applyModifier($qb, $modifier);

        return $this->applyHints($qb->getQuery(), $modifier)->getOneOrNullResult();
    }

    /**
     * @return Article[]
     */
    public function findArticles(?ArticleQueryModifier $modifier = null): array
    {
        return $this->findArticlesQuery($modifier)->getResult();
    }

    public function countArticles(): int
    {
        return $this->count([]);
    }

    public function findArticlesQuery(?ArticleQueryModifier $modifier = null): Query
    {
        $qb = $this->createQueryBuilder('a');

        $this->applyModifier($qb, $modifier);

        return $this->applyHints($qb->getQuery(), $modifier);
    }

    public function findArticlesBySection(int $sectionId)
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.section = :section')
            ->setParameter('section', $sectionId)
        ;

        return $this->applyHints($qb->getQuery())->getResult();
    }

    public function applyModifier(QueryBuilder $qb, ?ArticleQueryModifier $modifier, string $alias = 'a'): void
    {
        if (!$modifier) {
            return;
        }

        if ($modifier->select) {
            $qb->select(
                array_map(static function ($field) use ($alias) {
                        return "$alias.$field";
                    }, $modifier->select)
            );
        }
        if ($modifier->orderByField) {
            $qb->addOrderBy("$alias.{$modifier->orderByField}", $modifier->orderDirection ?: 'ASC');
        }
        if ($modifier->withSection) {
            $qb->addSelect('a_section')
                ->leftJoin("$alias.section", 'a_section');
        }
        if ($modifier->withOwner) {
            $qb->addSelect('a_owner')
                ->leftJoin("$alias.owner", 'a_owner');
        }
        if ($modifier->withComments) {
            $qb->addSelect('a_comments')
                ->leftJoin("$alias.comments", 'a_comments');
        }
        if ($modifier->withImages) {
            $qb->addSelect(['a_images', 'a_image_references'])
                ->leftJoin("$alias.images", 'a_image_references')
                ->leftJoin('a_image_references.image', 'a_images');
        }
        if ($modifier->limit) {
            $qb->setMaxResults($modifier->limit);
        }
        if ($modifier->comments) {
            $this->commentRepository->applyModifier($qb, $modifier->comments, 'a_comments');
        }
    }

    private function applyHints(Query $query, ?ArticleQueryModifier $modifier = null): Query
    {
        $query = $this->applyTranslatables($query, $modifier);

        $query->setHint('knp_paginator.count', $this->countArticles());

        return $query;
    }
}
