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
     * combination with parent::getPathQuery() and parent::getPath()
     * with applying filters and translation hints
     *
     * @param Section $section
     * @return Section[]
     */
    public function getSectionPath(Section $section): array
    {
        $meta = $this->getClassMetadata();
        $config = $this->listener->getConfiguration($this->_em, $meta->name);
        $closureMeta = $this->_em->getClassMetadata($config['closure']);

        $dql = "SELECT c, node FROM {$closureMeta->name} c";
        $dql .= " INNER JOIN c.ancestor node";
        $dql .= " WHERE c.descendant = :node";
        $dql .= " AND node.status = :status"; // here are status setting!
        $dql .= " ORDER BY c.depth DESC";
        $q = $this->_em->createQuery($dql);
        $q->setParameters(['node' => $section, 'status' => 'visible']);

        return array_map(static function (AbstractClosure $closure) {
            return $closure->getAncestor();
        }, $this->applyHints($q)->getResult());
    }

    /**
     * @return Section[]
     */
    public function findAllPublic(): array
    {
        $qb = $this->createQueryBuilder('s');

        return $this->apply($qb, 's')->getResult();
    }

    public function findForAdmin(int $id, string $locale): ?Section
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.id = :id')
            ->setParameter('id', $id);

        return $this->applyHints($qb->getQuery(), false, $locale)->getOneOrNullResult();
    }

    public function findPublic(string $slug): ?Section
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.slug = :slug')
            ->setParameter('slug', $slug)
        ;

        return $this->apply($qb, 's')->getOneOrNullResult();
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

        return $this->apply($qb, 's')->getResult();
    }

    /**
     * @return Section[]
     */
    public function getRootSections(): array
    {
        $qb = $this->getRootNodesQueryBuilder('position', 'asc');

        return $this->apply($qb, 'node')->getResult();
    }

    private function apply(QueryBuilder $qb, $sectionAlias = 's'): Query
    {
        return $this->applyHints($this->applyFilter($qb, $sectionAlias)->getQuery());
    }

    private function applyFilter(QueryBuilder $qb, $sectionAlias = 's'): QueryBuilder
    {
        $qb
            ->andWhere(sprintf('%s.status = :status', $sectionAlias))
            ->setParameter('status', 'visible')
        ;

        return $qb;
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
