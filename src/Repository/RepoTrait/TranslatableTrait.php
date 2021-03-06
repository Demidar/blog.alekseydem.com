<?php

namespace App\Repository\RepoTrait;

use App\Repository\ModifierParams\TranslatableQueryModifierParams;
use Doctrine\ORM\Query;
use Gedmo\Translatable\Query\TreeWalker\TranslationWalker;
use Gedmo\Translatable\TranslatableListener;
use Symfony\Component\OptionsResolver\OptionsResolver;

trait TranslatableTrait
{
    protected function applyTranslatables(Query $query, ?TranslatableQueryModifierParams $modifier = null): Query
    {
        if ($modifier) {
            $locale = $modifier->getLocale();
            if ($modifier->getFallback() !== null) {
                $fallback = $modifier->getFallback();
            } else {
                $fallback = true;
            }
        } else {
            $locale = null;
            $fallback = true;
        }

        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, TranslationWalker::class);

        $query->setHint(TranslatableListener::HINT_FALLBACK, $fallback);
        if ($locale) {
            $query->setHint(TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale);
        }

        return $query;
    }
}
