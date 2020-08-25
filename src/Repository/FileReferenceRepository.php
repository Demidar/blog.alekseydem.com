<?php

namespace App\Repository;

use App\Entity\FileReference;
use App\Repository\Modifier\FileReferenceQueryModifier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FileReference|null find($id, $lockMode = null, $lockVersion = null)
 * @method FileReference|null findOneBy(array $criteria, array $orderBy = null)
 * @method FileReference[]    findAll()
 * @method FileReference[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileReferenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, $entityClass = null)
    {
        parent::__construct($registry, $entityClass ?? FileReference::class);
    }

    public function findFileReferenceById(int $id, ?FileReferenceQueryModifier $modifier = null): ?FileReference
    {
        $qb = $this->createQueryBuilder('fr')
            ->andWhere('fr.id = :id')
            ->setParameter('id', $id)
        ;

        $this->applyModifier($qb, $modifier);

        return $this->applyHints($qb->getQuery(), $modifier)->getOneOrNullResult();
    }

    /**
     * @return FileReference[]
     */
    public function findFileReferences(?FileReferenceQueryModifier $modifier = null): array
    {
        return $this->findFileReferencesQuery($modifier)->getResult();
    }

    public function countFileReferences(): int
    {
        return $this->count([]);
    }

    public function findFileReferencesQuery(?FileReferenceQueryModifier $modifier = null): Query
    {
        $qb = $this->createQueryBuilder('fr');

        $this->applyModifier($qb, $modifier);

        return $this->applyHints($qb->getQuery(), $modifier);
    }

    private function applyModifier(QueryBuilder $qb, ?FileReferenceQueryModifier $modifier): void
    {
        if (!$modifier) {
            return;
        }
    }

    private function applyHints(Query $query, ?FileReferenceQueryModifier $modifier = null): Query
    {
        $query->setHint('knp_paginator.count', $this->countFileReferences());

        return $query;
    }
}
