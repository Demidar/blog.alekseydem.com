<?php

namespace App\Repository\ModifierParams;

use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentQueryModifierParams implements TranslatableQueryModifierParams
{
    public ?bool $withOwner;
    public ?bool $withParent;
    public ?bool $withArticle;
    public ?string $orderByField;
    public ?string $orderDirection;
    public ?bool $fallback;
    public ?string $locale;

    public function __construct(array $modifiersArray = null)
    {
        $options = (new OptionsResolver())->setDefaults([
            'withOwner' => null,
            'withParent' => null,
            'withArticle' => null,
            'orderByField' => null,
            'orderDirection' => null,
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
