<?php

namespace App\Repository\Modifier;

use App\Repository\ModifierParams\UserQueryModifierParams;
use Doctrine\ORM\QueryBuilder;

class UserQueryModifier
{
    public function applyModifier(QueryBuilder $qb, ?UserQueryModifierParams $modifierParams, string $alias): void
    {
        if (!$modifierParams) {
            return;
        }
        if ($modifierParams->withPhoto) {
            $qb->addSelect('u_photo')
                ->leftJoin("$alias.photo", 'u_photo');
        }
    }
}
