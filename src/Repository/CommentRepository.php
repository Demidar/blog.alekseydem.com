<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Tree\Entity\Repository\ClosureTreeRepository;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ClosureTreeRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getClassMetadata(Comment::class));
    }

    /**
     * @return Comment[]
     */
    public function findAllVisibleCommentsOfArticle(Article $article)
    {
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.article = :article')
            ->join('c.owner', 'o')
            ->setParameter('article', $article->getId())
        ;

        $this->applyFilter($qb, 'c');

        return $qb->getQuery()->getResult();
    }

    private function applyFilter(QueryBuilder $qb, $commentAlias = 'c'): void
    {
        $qb
            ->andWhere(sprintf('%s.status = :status', $commentAlias))
            ->setParameter('status', 'visible')
        ;
    }
}
