<?php

namespace App\Repository\Modifier;

use App\Repository\ModifierParams\ImageQueryModifierParams;
use Doctrine\ORM\QueryBuilder;

class ImageQueryModifier
{
    public function applyModifier(QueryBuilder $qb, ?ImageQueryModifierParams $modifier, string $alias): void
    {
        if (!$modifier) {
            return;
        }
    }
}
