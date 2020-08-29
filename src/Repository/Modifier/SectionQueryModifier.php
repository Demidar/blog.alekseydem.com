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

    public function applyModifier(QueryBuilder $qb, ?SectionQueryModifierParams $modifier, string $alias = 's'): void
    {
        if (!$modifier) {
            return;
        }
        if ($modifier->withParent) {
            $qb->addSelect('s_parent')
                ->leftJoin("$alias.parent", 's_parent');
        }
        if ($modifier->withArticles) {
            $qb->addSelect('s_articles')
                ->leftJoin("$alias.articles", 's_articles');
            if ($modifier->articles) {
                $this->articleQueryModifier->applyModifier($qb, $modifier->articles, 's_articles');
            }
        }
    }
}
