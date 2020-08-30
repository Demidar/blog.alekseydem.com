<?php

namespace App\Repository\JoinConditionParams;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleJoinConditionParams
{
    public ?string $conjunction;
    /** @var string[]|null */
    public ?array $findExceptSlugs;

    public function __construct(array $modifiersArray = null)
    {
        $options = (new OptionsResolver())->setDefaults([
            'findExceptSlugs' => null,
            'conjunction' => 'AND'
        ])->resolve($modifiersArray);
        foreach ($options as $key => $value) {
            $this->$key = $value;
        }
    }
}
