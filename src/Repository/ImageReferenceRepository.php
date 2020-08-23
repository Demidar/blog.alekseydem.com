<?php

namespace App\Repository;

use App\Entity\ImageReference;
use App\Repository\Filter\ImageReferenceFilter;
use App\Repository\Modifier\ImageReferenceQueryModifier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImageReference|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImageReference|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImageReference[]    findAll()
 * @method ImageReference[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageReferenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, $entityClass = null)
    {
        parent::__construct($registry, $entityClass ?? ImageReference::class);
    }

    public function findImageReferenceById(int $id, ?ImageReferenceFilter $filter = null, ?ImageReferenceQueryModifier $modifier = null): ?ImageReference
    {
        $qb = $this->createQueryBuilder('fr')
            ->andWhere('fr.id = :id')
            ->setParameter('id', $id)
        ;

        $this->applyModifier($qb, $modifier);

        return $this->applyHints($qb->getQuery(), $filter)->getOneOrNullResult();
    }

    /**
     * @return ImageReference[]
     */
    public function findImageReferences(?ImageReferenceFilter $filter = null, ?ImageReferenceQueryModifier $modifier = null): array
    {
        return $this->findImageReferencesQuery($filter, $modifier)->getResult();
    }

    public function countImageReferences(): int
    {
        return $this->count([]);
    }

    public function findImageReferencesQuery(?ImageReferenceFilter $filter = null, ?ImageReferenceQueryModifier $modifier = null): Query
    {
        $qb = $this->createQueryBuilder('fr');

        $this->applyModifier($qb, $modifier);

        return $this->applyHints($qb->getQuery(), $filter);
    }

    private function applyModifier(QueryBuilder $qb, ?ImageReferenceQueryModifier $modifier): void
    {
        if (!$modifier) {
            return;
        }
    }

    private function applyHints(Query $query, ?ImageReferenceFilter $filter = null): Query
    {
        $query->setHint('knp_paginator.count', $this->countImageReferences());

        return $query;
    }
}
