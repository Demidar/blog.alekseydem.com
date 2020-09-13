<?php

namespace App\Repository\Interfaces;

use App\Entity\User;
use App\Repository\ModifierParams\UserQueryModifierParams;
use Doctrine\ORM\Query;

interface UserQueryingInterface
{
    public function findUserById(int $id, ?UserQueryModifierParams $modifierParams): ?User;

    /**
     * @return User[]
     */
    public function findUsers(?UserQueryModifierParams $modifierParams = null): array;

    public function getUsersQuery(?UserQueryModifierParams $modifierParams = null): Query;
}
