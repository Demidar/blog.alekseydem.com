<?php

namespace App\Repository;

use App\Entity\File;
use App\Repository\Filter\FileFilter;
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

    public function findFileById(int $id, ?FileFilter $filter = null, ?FileQueryModifier $modifier = null): ?File
    {
        $qb = $this->createQueryBuilder('f')
            ->andWhere('f.id = :id')
            ->setParameter('id', $id)
        ;

        $this->applyModifier($qb, $modifier);

        return $this->applyHints($qb->getQuery(), $filter)->getOneOrNullResult();
    }

    /**
     * @return File[]
     */
    public function findFiles(?FileFilter $filter = null, ?FileQueryModifier $modifier = null): array
    {
        return $this->findFilesQuery($filter, $modifier)->getResult();
    }

    public function countFiles(): int
    {
        return $this->count([]);
    }

    public function findFilesQuery(?FileFilter $filter = null, ?FileQueryModifier $modifier = null): Query
    {
        $qb = $this->createQueryBuilder('f');

        $this->applyModifier($qb, $modifier);

        return $this->applyHints($qb->getQuery(), $filter);
    }

    private function applyModifier(QueryBuilder $qb, ?FileQueryModifier $modifier): void
    {
        if (!$modifier) {
            return;
        }
    }

    private function applyHints(Query $query, ?FileFilter $filter = null): Query
    {
        $query->setHint('knp_paginator.count', $this->countFiles());

        return $query;
    }
}
