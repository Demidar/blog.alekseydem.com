<?php

namespace App\Repository\RepoTrait;

use App\Repository\Filter\TranslatableFilter;
use Doctrine\ORM\Query;
use Gedmo\Translatable\Query\TreeWalker\TranslationWalker;
use Gedmo\Translatable\TranslatableListener;

trait TranslatableTrait
{
    protected function applyTranslatables(Query $query, TranslatableFilter $filter = null): Query
    {
        $locale = $filter ? $filter->getLocale() : null;
        $fallback = $filter ? $filter->getFallback() : true;

        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, TranslationWalker::class);

        if ($fallback) {
            $query->setHint(TranslatableListener::HINT_FALLBACK, 1);
        } else {
            $query->setHint(TranslatableListener::HINT_FALLBACK, 0);
        }
        if ($locale) {
            $query->setHint(TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale);
        }

        return $query;
    }
}
