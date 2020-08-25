<?php

namespace App\Repository;

use App\Entity\ImageReference;
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

    public function findImageReferenceById(int $id, ?ImageReferenceQueryModifier $modifier = null): ?ImageReference
    {
        $qb = $this->createQueryBuilder('ir')
            ->andWhere('ir.id = :id')
            ->setParameter('id', $id)
        ;

        $this->applyModifier($qb, $modifier);

        return $this->applyHints($qb->getQuery(), $modifier)->getOneOrNullResult();
    }

    /**
     * @return ImageReference[]
     */
    public function findImageReferences(?ImageReferenceQueryModifier $modifier = null): array
    {
        return $this->findImageReferencesQuery($modifier)->getResult();
    }

    public function countImageReferences(): int
    {
        return $this->count([]);
    }

    public function findImageReferencesQuery(?ImageReferenceQueryModifier $modifier = null): Query
    {
        $qb = $this->createQueryBuilder('fr');

        $this->applyModifier($qb, $modifier);

        return $this->applyHints($qb->getQuery(), $modifier);
    }

    private function applyModifier(QueryBuilder $qb, ?ImageReferenceQueryModifier $modifier, string $alias = 'ir'): void
    {
        if (!$modifier) {
            return;
        }
    }

    private function applyHints(Query $query, ?ImageReferenceQueryModifier $modifier = null): Query
    {
        $query->setHint('knp_paginator.count', $this->countImageReferences());

        return $query;
    }
}
