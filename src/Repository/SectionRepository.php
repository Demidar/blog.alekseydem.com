<?php

namespace App\Repository;

use App\Entity\Section;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Translatable\Query\TreeWalker\TranslationWalker;
use Gedmo\Translatable\TranslatableListener;
use Gedmo\Tree\Entity\MappedSuperclass\AbstractClosure;
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
     * parent::getPath() with applying translation hints
     *
     * @param Section $section
     * @return Section[]
     */
    public function getSectionPathWithLocale(Section $section): array
    {
        return array_map(static function (AbstractClosure $closure) {
            return $closure->getAncestor();
        }, $this->applyHints($this->getPathQuery($section))->getResult());
    }

    public function findWithLocaleById(int $id, ?string $locale = null, bool $fallback = true): ?Section
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.id = :id')
            ->setParameter('id', $id);

        return $this->applyHints($qb->getQuery(), $fallback, $locale)->getOneOrNullResult();
    }

    public function findWithLocaleBySlug(string $slug, ?string $locale = null, bool $fallback = true): ?Section
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.slug = :slug')
            ->setParameter('slug', $slug)
        ;

        return $this->applyHints($qb->getQuery(), $fallback, $locale)->getOneOrNullResult();
    }

    /**
     * @return Section[]
     */
    public function findChildrenWithLocale(int $id): array
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.parent = :id')
            ->setParameter('id', $id)
        ;

        return $this->applyHints($qb->getQuery())->getResult();
    }

    /**
     * @return Section[]
     */
    public function getRootSectionsWithLocale(): array
    {
        $qb = $this->getRootNodesQueryBuilder('position', 'asc');

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
