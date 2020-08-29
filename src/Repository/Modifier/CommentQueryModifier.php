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
            $qb->addSelect('c_owner')
                ->leftJoin("$alias.owner", 'c_owner');
        }
        if ($modifier->withParent) {
            $qb->addSelect('c_parent')
                ->leftJoin("$alias.parent", 'c_parent');
        }
        if ($modifier->withArticle) {
            $qb->addSelect('c_article')
                ->leftJoin("$alias.article", 'c_article');
        }
        if ($modifier->orderByField) {
            $qb->addOrderBy("$alias.{$modifier->orderByField}", $modifier->orderDirection ?: 'ASC');
        }
    }
}
