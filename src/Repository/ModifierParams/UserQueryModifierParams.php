<?php

namespace App\Repository\ModifierParams;

use Symfony\Component\OptionsResolver\OptionsResolver;

class UserQueryModifierParams
{
    public ?bool $withPhoto;

    public function __construct(array $modifiersArray = null)
    {
        $options = (new OptionsResolver())->setDefaults([
            'withPhoto' => null,
        ])->resolve($modifiersArray);
        foreach ($options as $key => $value) {
            $this->$key = $value;
        }
    }
}
