<?php

namespace App\Repository;

use App\Entity\Image;
use App\Repository\Interfaces\ImageQueryingInterface;
use App\Repository\Modifier\ImageQueryModifier;
use App\Repository\ModifierParams\ImageQueryModifierParams;
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
class ImageRepository extends ServiceEntityRepository implements ImageQueryingInterface
{
    private ImageQueryModifier $queryModifier;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }

    /**
     * @required
     */
    public function setQueryModifier(ImageQueryModifier $queryModifier): void
    {
        $this->queryModifier = $queryModifier;
    }

    public function findImageById(int $id, ?ImageQueryModifierParams $modifier = null): ?Image
    {
        $qb = $this->createQueryBuilder('i')
            ->andWhere('i.id = :id')
            ->setParameter('id', $id)
        ;

        $this->queryModifier->applyModifier($qb, $modifier, 'i');

        return $this->applyHints($qb->getQuery(), $modifier)->getOneOrNullResult();
    }

    /**
     * @return Image[]
     */
    public function findImages(?ImageQueryModifierParams $modifier = null): array
    {
        return $this->findImagesQuery($modifier)->getResult();
    }

    public function countImages(): int
    {
        return $this->count([]);
    }

    public function findImagesQuery(?ImageQueryModifierParams $modifier = null): Query
    {
        $qb = $this->createQueryBuilder('i');

        $this->queryModifier->applyModifier($qb, $modifier, 'i');

        return $this->applyHints($qb->getQuery(), $modifier);
    }

    private function applyHints(Query $query, ?ImageQueryModifierParams $modifier = null): Query
    {
        $query->setHint('knp_paginator.count', $this->countImages());

        return $query;
    }
}
