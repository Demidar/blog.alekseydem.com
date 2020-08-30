<?php

namespace App\Repository;

use App\Entity\Article;
use App\Repository\Modifier\ArticleQueryModifier;
use App\Repository\ModifierParams\ArticleQueryModifierParams;
use App\Repository\RepoTrait\TranslatableTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
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

    private $queryModifier;

    public function __construct(
        ManagerRegistry $registry,
        ArticleQueryModifier $queryModifier
    ) {
        parent::__construct($registry, Article::class);
        $this->queryModifier = $queryModifier;
    }

    public function findArticleBySlug(string $slug, ?ArticleQueryModifierParams $modifierParams = null): ?Article
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere("a.slug = :slug")
            ->setParameter('slug', $slug)
        ;

        $this->queryModifier->applyModifier($qb, $modifierParams, 'a');

        return $this->applyHints($qb->getQuery(), $modifierParams)->getOneOrNullResult();
    }

    public function findArticleById(string $id, ?ArticleQueryModifierParams $modifierParams = null): ?Article
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.id = :id')
            ->addSelect('s')
            ->leftJoin('a.section', 's')
            ->setParameter('id', $id)
        ;

        $this->queryModifier->applyModifier($qb, $modifierParams, 'a');

        return $this->applyHints($qb->getQuery(), $modifierParams)->getOneOrNullResult();
    }

    /**
     * @return Article[]
     */
    public function findArticles(?ArticleQueryModifierParams $modifierParams = null): array
    {
        return $this->getArticlesQuery($modifierParams)->getResult();
    }

    public function countArticles(): int
    {
        return $this->count([]);
    }

    public function getArticlesQuery(?ArticleQueryModifierParams $modifierParams = null): Query
    {
        $qb = $this->createQueryBuilder('a');

        $this->queryModifier->applyModifier($qb, $modifierParams, 'a');

        return $this->applyHints($qb->getQuery(), $modifierParams);
    }

    /**
     * @return Article[]
     */
    public function findArticlesBySection(int $sectionId, ?ArticleQueryModifierParams $modifierParams = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.section = :section')
            ->setParameter('section', $sectionId)
        ;

        $this->queryModifier->applyModifier($qb, $modifierParams, 'a');

        return $this->applyHints($qb->getQuery())->getResult();
    }

    private function applyHints(Query $query, ?ArticleQueryModifierParams $modifier = null): Query
    {
        $query = $this->applyTranslatables($query, $modifier);

        $query->setHint('knp_paginator.count', $this->countArticles());

        return $query;
    }
}
