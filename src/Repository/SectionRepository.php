<?php

namespace App\Repository;

use App\Entity\Section;
use App\Repository\Filter\SectionFilter;
use App\Repository\Modifier\SectionQueryModifier;
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
     * @return Section[]
     */
    public function getSectionPath(Section $section, SectionFilter $filter = null): array
    {
        return array_map(static function (AbstractClosure $closure) {
            return $closure->getAncestor();
        }, $this->applyHints($this->getPathQuery($section), $filter)->getResult());
    }

    public function findSectionById(int $id, SectionFilter $filter = null): ?Section
    {

        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.id = :id')
            ->setParameter('id', $id);

        return $this->applyHints($qb->getQuery(), $filter)->getOneOrNullResult();
    }

    public function findSectionBySlug(string $slug, SectionFilter $filter = null): ?Section
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.slug = :slug')
            ->setParameter('slug', $slug)
        ;

        return $this->applyHints($qb->getQuery(), $filter)->getOneOrNullResult();
    }

    /**
     * @return Section[]
     */
    public function findSections(SectionFilter $filter = null): array
    {
        return $this->findSectionsQuery($filter)->getResult();
    }

    public function findSectionsQuery(?SectionFilter $filter = null, ?SectionQueryModifier $modifier = null): Query
    {
        $qb = $this->createQueryBuilder('s');

        $this->applyModifier($qb, $modifier);

        return $this->applyHints($qb->getQuery(), $filter);
    }

    /**
     * @return Section[]
     */
    public function findChildren(int $id, SectionFilter $filter = null): array
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.parent = :id')
            ->setParameter('id', $id)
        ;

        return $this->applyHints($qb->getQuery(), $filter)->getResult();
    }

    /**
     * @return Section[]
     */
    public function getRootSections(SectionFilter $filter = null): array
    {
        $qb = $this->getRootNodesQueryBuilder('position', 'asc');

        return $this->applyHints($qb->getQuery(), $filter)->getResult();
    }

    private function applyModifier(QueryBuilder $qb, ?SectionQueryModifier $modifier): void
    {
        if (!$modifier) {
            return;
        }
        if ($modifier->withParent) {
            $qb->addSelect('p')
                ->leftJoin('s.parent', 'p');
        }
    }

    private function applyHints(Query $query, ?SectionFilter $filter = null): Query
    {
        $locale = $filter->locale ?? null;
        $fallback = $filter->fallback ?? true;

        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, TranslationWalker::class);

        if ($fallback) {
            $query->setHint(TranslatableListener::HINT_FALLBACK, 1);
        }
        if ($locale) {
            $query->setHint(TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale);
        }

        return $query;
    }
}
