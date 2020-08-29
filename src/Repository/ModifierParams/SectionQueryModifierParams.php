<?php

namespace App\Repository\ModifierParams;

use Symfony\Component\OptionsResolver\OptionsResolver;

class SectionQueryModifierParams implements TranslatableQueryModifierParams
{
    public ?bool $withParent;
    public ?bool $withArticles;
    public ?ArticleQueryModifierParams $articles;
    public ?bool $fallback;
    public ?string $locale;
    /** @var integer[]|null  */
    public ?array $findExceptIds;

    public function __construct(array $modifiersArray = null)
    {
        $options = (new OptionsResolver())->setDefaults([
            'withParent' => null,
            'withArticles' => null,
            'articles' => null,
            'fallback' => null,
            'locale' => null,
            'findExceptIds' => null
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
