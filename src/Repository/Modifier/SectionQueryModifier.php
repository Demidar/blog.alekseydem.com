<?php

namespace App\Repository\Modifier;

use Symfony\Component\OptionsResolver\OptionsResolver;

class SectionQueryModifier implements TranslatableQueryModifier
{
    public ?bool $withParent;
    public ?bool $withArticles;
    public ?ArticleQueryModifier $articles;
    public ?bool $fallback;
    public ?string $locale;

    public function __construct(array $modifiersArray = null)
    {
        $options = (new OptionsResolver())->setDefaults([
            'withParent' => null,
            'withArticles' => null,
            'articles' => null,
            'fallback' => null,
            'locale' => null,
        ])->resolve($modifiersArray);
        foreach ($options as $key => $value) {
            $this->$key = $value;
        }
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function getFallback(): ?bool
    {
        return $this->fallback;
    }
}
