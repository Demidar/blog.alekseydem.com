<?php

namespace App\Repository\Modifier;

use App\Repository\ModifierParams\FileReferenceQueryModifierParams;
use Doctrine\ORM\QueryBuilder;

class FileReferenceQueryModifier
{
    public function applyModifier(QueryBuilder $qb, ?FileReferenceQueryModifierParams $modifier, string $alias): void
    {
        if (!$modifier) {
            return;
        }
    }
}
