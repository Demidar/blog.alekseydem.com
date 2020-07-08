<?php

namespace App\Repository;

use App\Entity\Section;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Tree\Entity\Repository\ClosureTreeRepository;

/**
 * @method Section|null find($id, $lockMode = null, $lockVersion = null)
 * @method Section|null findOneBy(array $criteria, array $orderBy = null)
 * @method Section[]    findAll()
 * @method Section[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SectionRepository extends ClosureTreeRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getClassMetadata(Section::class));
    }

    /**
     * @return Section[]
     */
    public function findAllPublic(): array
    {
        $qb = $this->createQueryBuilder('s');

        $this->applyFilter($qb);

        return $qb->getQuery()->getResult();
    }

    public function findPublic(string $slug): ?Section
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.slug = :slug')
            ->setParameter('slug', $slug)
        ;

        $this->applyFilter($qb, 's');

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Section[]
     */
    public function findChildrenPublic(int $id): array
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.parent = :id')
            ->setParameter('id', $id)
        ;

        $this->applyFilter($qb, 's');

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Section[]
     */
    public function getRootSections(): array
    {
        $qb = $this->getRootNodesQueryBuilder('position', 'asc');

        $this->applyFilter($qb, 'node');

        return $qb->getQuery()->getResult();
    }

    private function applyFilter(QueryBuilder $qb, $sectionAlias = 's'): void
    {
        $qb
            ->andWhere(sprintf('%s.status = :status', $sectionAlias))
            ->setParameter('status', 'visible')
        ;
    }
}
