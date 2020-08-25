<?php

namespace App\Repository;

use App\Entity\Section;
use App\Repository\Modifier\SectionQueryModifier;
use App\Repository\RepoTrait\TranslatableTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
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
    use TranslatableTrait;

    private $articleRepository;

    public function __construct(
        EntityManagerInterface $em,
        ArticleRepository $articleRepository
    ) {
        parent::__construct($em, $em->getClassMetadata(Section::class));
        $this->articleRepository = $articleRepository;
    }

    /**
     * parent::getPath() with applying translation hints
     *
     * @return Section[]
     */
    public function getSectionPath(Section $section, ?SectionQueryModifier $modifier = null): array
    {
        return array_map(static function (AbstractClosure $closure) {
            return $closure->getAncestor();
        }, $this->applyHints($this->getPathQuery($section), $modifier)->getResult());
    }

    public function findSectionById(int $id, ?SectionQueryModifier $modifier = null): ?Section
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.id = :id')
            ->setParameter('id', $id);

        $this->applyModifier($qb, $modifier);

        return $this->applyHints($qb->getQuery(), $modifier)->getOneOrNullResult();
    }

    public function findSectionBySlug(string $slug, ?SectionQueryModifier $modifier = null): ?Section
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.slug = :slug')
            ->setParameter('slug', $slug)
        ;

        $this->applyModifier($qb, $modifier);

        return $this->applyHints($qb->getQuery(), $modifier)->getOneOrNullResult();
    }

    /**
     * @return Section[]
     */
    public function findSections(?SectionQueryModifier $modifier = null): array
    {
        return $this->findSectionsQuery($modifier)->getResult();
    }

    public function findSectionsQuery(?SectionQueryModifier $modifier = null): Query
    {
        $qb = $this->createQueryBuilder('s');

        $this->applyModifier($qb, $modifier);

        return $this->applyHints($qb->getQuery(), $modifier);
    }

    /**
     * @return Section[]
     */
    public function findChildren(int $id, ?SectionQueryModifier $modifier = null): array
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.parent = :id')
            ->setParameter('id', $id)
        ;

        $this->applyModifier($qb, $modifier);

        return $this->applyHints($qb->getQuery(), $modifier)->getResult();
    }

    /**
     * @return Section[]
     */
    public function getRootSections(?SectionQueryModifier $modifier = null): array
    {
        $qb = $this->getRootNodesQueryBuilder('position', 'asc');

        $this->applyModifier($qb, $modifier, 'node');

        return $this->applyHints($qb->getQuery(), $modifier)->getResult();
    }

    private function applyModifier(QueryBuilder $qb, ?SectionQueryModifier $modifier, string $alias = 's'): void
    {
        if (!$modifier) {
            return;
        }
        if ($modifier->withParent) {
            $qb->addSelect('section_parent')
                ->leftJoin("$alias.parent", 'section_parent');
        }
        if ($modifier->withArticles) {
            $qb->addSelect('section_articles')
                ->leftJoin("$alias.articles", 'section_articles');
            if ($modifier->articles) {
                $this->articleRepository->applyModifier($qb, $modifier->articles, 'section_articles');
            }
        }
    }

    private function applyHints(Query $query, ?SectionQueryModifier $modifier = null): Query
    {
        $query = $this->applyTranslatables($query, $modifier);

        return $query;
    }
}
