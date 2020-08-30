<?php

namespace App\Repository\Modifier;

use App\Repository\JoinCondition\ArticleJoinCondition;
use App\Repository\ModifierParams\SectionQueryModifierParams;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

class SectionQueryModifier
{
    private $articleQueryModifier;
    private $articleJoinCondition;

    public function __construct(ArticleQueryModifier $articleQueryModifier, ArticleJoinCondition $articleJoinCondition)
    {
        $this->articleQueryModifier = $articleQueryModifier;
        $this->articleJoinCondition = $articleJoinCondition;
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
            $joinClause = $this->articleJoinCondition->getCondition($modifier->withArticles, "{$alias}_articles");
            $qb->addSelect("{$alias}_articles")
                ->leftJoin("$alias.articles", "{$alias}_articles", Join::WITH, $joinClause);
        }
        if ($modifier->articles) {
            $this->articleQueryModifier->applyModifier($qb, $modifier->articles, "{$alias}_articles");
        }
        if ($modifier->findExceptIds) {
            $qb->andWhere($qb->expr()->notIn("$alias.id", $modifier->findExceptIds));
        }
    }
}
