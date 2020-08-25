<?php

namespace App\Repository\Modifier;

use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentQueryModifier implements TranslatableQueryModifier
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
