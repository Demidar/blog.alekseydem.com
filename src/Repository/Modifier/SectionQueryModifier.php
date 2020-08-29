<?php

namespace App\Repository\Modifier;

use App\Repository\ModifierParams\SectionQueryModifierParams;
use Doctrine\ORM\QueryBuilder;

class SectionQueryModifier
{
    private $articleQueryModifier;

    public function __construct(ArticleQueryModifier $articleQueryModifier)
    {
        $this->articleQueryModifier = $articleQueryModifier;
    }

    public function applyModifier(QueryBuilder $qb, ?SectionQueryModifierParams $modifier, string $alias): void
    {
        if (!$modifier) {
            return;
        }
        if ($modifier->withParent) {
            $qb->addSelect("{$alias}_parent")
                ->leftJoin("$alias.parent", "{$alias}_parent");
        }
        if ($modifier->withArticles) {
            $qb->addSelect("{$alias}_articles")
                ->leftJoin("$alias.articles", "{$alias}_articles");
            if ($modifier->articles) {
                $this->articleQueryModifier->applyModifier($qb, $modifier->articles, "{$alias}_articles");
            }
        }
        if ($modifier->findExceptIds) {
            $qb->andWhere($qb->expr()->notIn("$alias.id", $modifier->findExceptIds));
        }
    }
}
