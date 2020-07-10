<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function findPublic(string $slug): ?Article
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.slug = :slug')
            ->setParameter('slug', $slug)
        ;

        $this->applyFilter($qb, 'a');

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findAllPublishedBySection($sectionId)
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.section = :section')
            ->setParameter('section', $sectionId)
        ;

        $this->applyFilter($qb, 'a');

        return $qb->getQuery()->getResult();
    }

    private function applyFilter(QueryBuilder $qb, $articleAlias = 'a'): void
    {
        $qb
            ->andWhere(sprintf('%s.status = :status', $articleAlias))
            ->setParameter('status', 'published')
        ;
    }
}
