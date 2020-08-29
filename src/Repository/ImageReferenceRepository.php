<?php

namespace App\Repository;

use App\Entity\ImageReference;
use App\Repository\Modifier\ImageReferenceQueryModifier;
use App\Repository\ModifierParams\ImageReferenceQueryModifierParams;
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
    private ImageReferenceQueryModifier $queryModifier;

    public function __construct(ManagerRegistry $registry, $entityClass = null)
    {
        parent::__construct($registry, $entityClass ?? ImageReference::class);
    }

    /**
     * @required
     */
    public function setQueryModifier(ImageReferenceQueryModifier $queryModifier): void
    {
        $this->queryModifier = $queryModifier;
    }

    public function findImageReferenceById(int $id, ?ImageReferenceQueryModifierParams $modifier = null): ?ImageReference
    {
        $qb = $this->createQueryBuilder('ir')
            ->andWhere('ir.id = :id')
            ->setParameter('id', $id)
        ;

        $this->queryModifier->applyModifier($qb, $modifier, 'ir');

        return $this->applyHints($qb->getQuery(), $modifier)->getOneOrNullResult();
    }

    /**
     * @return ImageReference[]
     */
    public function findImageReferences(?ImageReferenceQueryModifierParams $modifier = null): array
    {
        return $this->findImageReferencesQuery($modifier)->getResult();
    }

    public function countImageReferences(): int
    {
        return $this->count([]);
    }

    public function findImageReferencesQuery(?ImageReferenceQueryModifierParams $modifier = null): Query
    {
        $qb = $this->createQueryBuilder('ir');

        $this->queryModifier->applyModifier($qb, $modifier, 'ir');

        return $this->applyHints($qb->getQuery(), $modifier);
    }

    private function applyHints(Query $query, ?ImageReferenceQueryModifierParams $modifier = null): Query
    {
        $query->setHint('knp_paginator.count', $this->countImageReferences());

        return $query;
    }
}
