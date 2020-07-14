<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Translatable\Query\TreeWalker\TranslationWalker;
use Gedmo\Translatable\TranslatableListener;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    private $commentRepository;

    public function __construct(
        ManagerRegistry $registry,
        CommentRepository $commentRepository
    ) {
        parent::__construct($registry, Article::class);
        $this->commentRepository = $commentRepository;
    }

    public function findPublic(string $slug): ?Article
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.slug = :slug')
            ->setParameter('slug', $slug)
        ;

        $this->applyFilter($qb, 'a');

        return $this->applyHints($qb->getQuery())->getOneOrNullResult();
    }

    public function findPublicWithComments(string $slug): ?Article
    {
        $qb = $this->createQueryBuilder('a')
            ->addSelect('c', 'o')
            ->andWhere('a.slug = :slug')
            ->leftJoin('a.comments', 'c', Expr\Join::WITH, 'c.status = :commentStatus')
            ->leftJoin('c.owner', 'o')
            ->setParameter('slug', $slug)
            ->setParameter('commentStatus', 'visible')
        ;

        $this->applyFilter($qb, 'a');

        return $this->applyHints($qb->getQuery())->getOneOrNullResult();
    }

    public function findAllPublishedBySection($sectionId)
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.section = :section')
            ->setParameter('section', $sectionId)
        ;

        $this->applyFilter($qb, 'a');

        return $this->applyHints($qb->getQuery())->getResult();
    }

    private function applyFilter(QueryBuilder $qb, $articleAlias = 'a'): void
    {
        $qb
            ->andWhere(sprintf('%s.status = :status', $articleAlias))
            ->setParameter('status', 'published')
        ;
    }

    private function applyHints(Query $query): Query
    {
        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, TranslationWalker::class)
            ->setHint(TranslatableListener::HINT_FALLBACK, 1)
        ;

        return $query;
    }
}
