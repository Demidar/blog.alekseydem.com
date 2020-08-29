<?php

namespace App\Repository\ModifierParams;

use App\Repository\Modifier\UserQueryModifier;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleQueryModifierParams implements TranslatableQueryModifierParams
{
    public ?bool $withOwner;
    public ?UserQueryModifier $owner;
    public ?bool $withSection;
    public ?bool $withComments;
    public ?bool $withImages;
    public ?string $orderByField;
    public ?string $orderDirection;
    /** @var string[]|null */
    public ?array $select;
    public ?CommentQueryModifierParams $comments;
    public ?bool $fallback;
    public ?string $locale;
    public ?int $limit;
    public ?int $offset;

    public function __construct(array $modifiersArray = null)
    {
        $options = (new OptionsResolver())->setDefaults([
            'withOwner' => null,
            'owner' => null,
            'withSection' => null,
            'withComments' => null,
            'withImages' => null,
            'comments' => null,
            'orderByField' => null,
            'orderDirection' => null,
            'select' => null,
            'fallback' => null,
            'locale' => null,
            'limit' => null,
            'offset' => null
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
