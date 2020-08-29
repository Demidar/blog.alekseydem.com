<?php

namespace App\Repository\Modifier;

use App\Repository\ModifierParams\FileQueryModifierParams;
use Doctrine\ORM\QueryBuilder;

class FileQueryModifier
{
    public function applyModifier(QueryBuilder $qb, ?FileQueryModifierParams $modifier, string $alias): void
    {
        if (!$modifier) {
            return;
        }
    }
}
