<?php

namespace App\Repository;

use App\Entity\File;
use App\Repository\Modifier\FileQueryModifier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method File|null find($id, $lockMode = null, $lockVersion = null)
 * @method File|null findOneBy(array $criteria, array $orderBy = null)
 * @method File[]    findAll()
 * @method File[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, File::class);
    }

    public function findFileById(int $id, ?FileQueryModifier $modifier = null): ?File
    {
        $qb = $this->createQueryBuilder('f')
            ->andWhere('f.id = :id')
            ->setParameter('id', $id)
        ;

        $this->applyModifier($qb, $modifier);

        return $this->applyHints($qb->getQuery(), $modifier)->getOneOrNullResult();
    }

    /**
     * @return File[]
     */
    public function findFiles(?FileQueryModifier $modifier = null): array
    {
        return $this->findFilesQuery($modifier)->getResult();
    }

    public function countFiles(): int
    {
        return $this->count([]);
    }

    public function findFilesQuery(?FileQueryModifier $modifier = null): Query
    {
        $qb = $this->createQueryBuilder('f');

        $this->applyModifier($qb, $modifier);

        return $this->applyHints($qb->getQuery(), $modifier);
    }

    private function applyModifier(QueryBuilder $qb, ?FileQueryModifier $modifier): void
    {
        if (!$modifier) {
            return;
        }
    }

    private function applyHints(Query $query, ?FileQueryModifier $modifier = null): Query
    {
        $query->setHint('knp_paginator.count', $this->countFiles());

        return $query;
    }
}
