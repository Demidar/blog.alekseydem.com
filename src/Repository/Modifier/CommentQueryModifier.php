<?php

namespace App\Repository\Modifier;

use App\Repository\ModifierParams\CommentQueryModifierParams;
use Doctrine\ORM\QueryBuilder;

class CommentQueryModifier
{
    public function applyModifier(QueryBuilder $qb, ?CommentQueryModifierParams $modifier, string $alias): void
    {
        if (!$modifier) {
            return;
        }
        if ($modifier->withOwner) {
            $qb->addSelect("{$alias}_owner")
                ->leftJoin("$alias.owner", "{$alias}_owner");
        }
        if ($modifier->withParent) {
            $qb->addSelect("{$alias}_parent")
                ->leftJoin("$alias.parent", "{$alias}_parent");
        }
        if ($modifier->withArticle) {
            $qb->addSelect("{$alias}_article")
                ->leftJoin("$alias.article", "{$alias}_article");
        }
        if ($modifier->orderByField) {
            $qb->addOrderBy("$alias.{$modifier->orderByField}", $modifier->orderDirection ?: "ASC");
        }
    }
}
