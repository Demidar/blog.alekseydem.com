<?php

namespace App\Repository;

use App\Entity\Section;
use App\Repository\Interfaces\SectionQueryingInterface;
use App\Repository\Modifier\SectionQueryModifier;
use App\Repository\ModifierParams\SectionQueryModifierParams;
use App\Repository\RepoTrait\TranslatableTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Gedmo\Tree\Entity\MappedSuperclass\AbstractClosure;
use Gedmo\Tree\Entity\Repository\ClosureTreeRepository;

/**
 * @method Section|null find($id, $lockMode = null, $lockVersion = null)
 * @method Section|null findOneBy(array $criteria, array $orderBy = null)
 * @method Section[]    findAll()
 * @method Section[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SectionRepository extends ClosureTreeRepository implements SectionQueryingInterface
{
    use TranslatableTrait;

    private SectionQueryModifier $queryModifier;

    public function __construct(
        EntityManagerInterface $em
    ) {
        parent::__construct($em, $em->getClassMetadata(Section::class));
    }

    /**
     * @required
     */
    public function setQueryModifier(SectionQueryModifier $queryModifier)
    {
        $this->queryModifier = $queryModifier;
    }

    /**
     * parent::getPath() with applying translation hints
     *
     * @return Section[]
     */
    public function getSectionPath(Section $section, ?SectionQueryModifierParams $modifier = null): array
    {
        return array_map(static function (AbstractClosure $closure) {
            return $closure->getAncestor();
        }, $this->applyHints($this->getPathQuery($section), $modifier)->getResult());
    }

    public function findSectionById(int $id, ?SectionQueryModifierParams $modifier = null): ?Section
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.id = :id')
            ->setParameter('id', $id);

        $this->queryModifier->applyModifier($qb, $modifier, 's');

        return $this->applyHints($qb->getQuery(), $modifier)->getOneOrNullResult();
    }

    public function findSectionBySlug(string $slug, ?SectionQueryModifierParams $modifier = null): ?Section
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.slug = :slug')
            ->setParameter('slug', $slug)
        ;

        $this->queryModifier->applyModifier($qb, $modifier, 's');

        return $this->applyHints($qb->getQuery(), $modifier)->getOneOrNullResult();
    }

    /**
     * @return Section[]
     */
    public function findSections(?SectionQueryModifierParams $modifier = null): array
    {
        return $this->findSectionsQuery($modifier)->getResult();
    }

    public function findSectionsQuery(?SectionQueryModifierParams $modifier = null): Query
    {
        $qb = $this->createQueryBuilder('s');

        $this->queryModifier->applyModifier($qb, $modifier, 's');

        return $this->applyHints($qb->getQuery(), $modifier);
    }

    /**
     * @return Section[]
     */
    public function findChildren(int $id, ?SectionQueryModifierParams $modifier = null): array
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.parent = :id')
            ->setParameter('id', $id)
        ;

        $this->queryModifier->applyModifier($qb, $modifier, 's');

        return $this->applyHints($qb->getQuery(), $modifier)->getResult();
    }

    /**
     * @return Section[]
     */
    public function getRootSections(?SectionQueryModifierParams $modifier = null): array
    {
        $qb = $this->getRootNodesQueryBuilder('position', 'asc');

        $this->queryModifier->applyModifier($qb, $modifier, 'node');

        return $this->applyHints($qb->getQuery(), $modifier)->getResult();
    }

    private function applyHints(Query $query, ?SectionQueryModifierParams $modifier = null): Query
    {
        $query = $this->applyTranslatables($query, $modifier);

        return $query;
    }
}
