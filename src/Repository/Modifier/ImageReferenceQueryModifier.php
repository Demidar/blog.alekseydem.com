<?php

namespace App\Repository\Modifier;

use App\Repository\ModifierParams\ImageReferenceQueryModifierParams;
use Doctrine\ORM\QueryBuilder;

class ImageReferenceQueryModifier
{
    public function applyModifier(QueryBuilder $qb, ?ImageReferenceQueryModifierParams $modifier, string $alias): void
    {
        if (!$modifier) {
            return;
        }
    }
}
