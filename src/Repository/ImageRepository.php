<?php

namespace App\Repository;

use App\Entity\Image;
use App\Repository\Modifier\ImageQueryModifier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Image|null find($id, $lockMode = null, $lockVersion = null)
 * @method Image|null findOneBy(array $criteria, array $orderBy = null)
 * @method Image[]    findAll()
 * @method Image[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }

    public function findImageById(int $id, ?ImageQueryModifier $modifier = null): ?Image
    {
        $qb = $this->createQueryBuilder('i')
            ->andWhere('i.id = :id')
            ->setParameter('id', $id)
        ;

        $this->applyModifier($qb, $modifier);

        return $this->applyHints($qb->getQuery(), $modifier)->getOneOrNullResult();
    }

    /**
     * @return Image[]
     */
    public function findImages(?ImageQueryModifier $modifier = null): array
    {
        return $this->findImagesQuery($modifier)->getResult();
    }

    public function countImages(): int
    {
        return $this->count([]);
    }

    public function findImagesQuery(?ImageQueryModifier $modifier = null): Query
    {
        $qb = $this->createQueryBuilder('i');

        $this->applyModifier($qb, $modifier);

        return $this->applyHints($qb->getQuery(), $modifier);
    }

    private function applyModifier(QueryBuilder $qb, ?ImageQueryModifier $modifier, string $alias = 'i'): void
    {
        if (!$modifier) {
            return;
        }
    }

    private function applyHints(Query $query, ?ImageQueryModifier $modifier = null): Query
    {
        $query->setHint('knp_paginator.count', $this->countImages());

        return $query;
    }
}
