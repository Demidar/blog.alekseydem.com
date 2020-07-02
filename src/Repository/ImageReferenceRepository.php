<?php

namespace App\Repository;

use App\Entity\ImageReference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImageReference|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImageReference|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImageReference[]    findAll()
 * @method ImageReference[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageReferenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImageReference::class);
    }

    // /**
    //  * @return ImageReference[] Returns an array of ImageReference objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ImageReference
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
