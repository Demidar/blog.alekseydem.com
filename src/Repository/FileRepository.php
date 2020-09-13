<?php

namespace App\Repository;

use App\Entity\File;
use App\Repository\Interfaces\FileSourceInterface;
use App\Repository\Modifier\FileQueryModifier;
use App\Repository\ModifierParams\FileQueryModifierParams;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method File|null find($id, $lockMode = null, $lockVersion = null)
 * @method File|null findOneBy(array $criteria, array $orderBy = null)
 * @method File[]    findAll()
 * @method File[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileRepository extends ServiceEntityRepository implements FileSourceInterface
{
    private FileQueryModifier $queryModifier;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, File::class);
    }

    /**
     * @required
     */
    public function setQueryModifier(FileQueryModifier $queryModifier): void
    {
        $this->queryModifier = $queryModifier;
    }

    public function findFileById(int $id, ?FileQueryModifierParams $modifier = null): ?File
    {
        $qb = $this->createQueryBuilder('f')
            ->andWhere('f.id = :id')
            ->setParameter('id', $id)
        ;

        $this->queryModifier->applyModifier($qb, $modifier, 'f');

        return $this->applyHints($qb->getQuery(), $modifier)->getOneOrNullResult();
    }

    /**
     * @return File[]
     */
    public function findFiles(?FileQueryModifierParams $modifier = null): array
    {
        return $this->findFilesQuery($modifier)->getResult();
    }

    public function countFiles(): int
    {
        return $this->count([]);
    }

    public function findFilesQuery(?FileQueryModifierParams $modifier = null): Query
    {
        $qb = $this->createQueryBuilder('f');

        $this->queryModifier->applyModifier($qb, $modifier, 'f');

        return $this->applyHints($qb->getQuery(), $modifier);
    }

    private function applyHints(Query $query, ?FileQueryModifierParams $modifier = null): Query
    {
        $query->setHint('knp_paginator.count', $this->countFiles());

        return $query;
    }
}
