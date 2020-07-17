<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
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
    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, Article::class);
    }

    public function findWithLocaleBuSlug(string $slug): ?Article
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.slug = :slug')
            ->setParameter('slug', $slug)
        ;

        return $this->applyHints($qb->getQuery())->getOneOrNullResult();
    }

    public function findWithLocaleWithComments(string $slug): ?Article
    {
        $qb = $this->createQueryBuilder('a')
            ->addSelect('o', 'c', 'co')
            ->andWhere('a.slug = :slug')
            ->leftJoin('a.owner', 'o')
            ->leftJoin('a.comments', 'c', Expr\Join::WITH, 'c.status = :commentStatus')
            ->leftJoin('c.owner', 'co')
            ->setParameter('slug', $slug)
            ->setParameter('commentStatus', 'visible')
        ;

        return $this->applyHints($qb->getQuery())->getOneOrNullResult();
    }

    public function findAllWithLocaleBySection($sectionId)
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.section = :section')
            ->setParameter('section', $sectionId)
        ;

        return $this->applyHints($qb->getQuery())->getResult();
    }

    private function applyHints(Query $query, bool $withFallback = true, ?string $locale = null): Query
    {
        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, TranslationWalker::class);

        if ($withFallback) {
            $query->setHint(TranslatableListener::HINT_FALLBACK, 1);
        }
        if ($locale) {
            $query->setHint(TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale);
        }

        return $query;
    }
}
