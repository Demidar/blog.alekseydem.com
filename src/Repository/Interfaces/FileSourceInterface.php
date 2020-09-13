<?php

namespace App\Repository\Interfaces;


use App\Entity\File;
use App\Repository\ModifierParams\FileQueryModifierParams;
use Doctrine\ORM\Query;

interface FileSourceInterface
{
    public function findFileById(int $id, ?FileQueryModifierParams $modifier = null): ?File;

    /**
     * @return File[]
     */
    public function findFiles(?FileQueryModifierParams $modifier = null): array;

    public function findFilesQuery(?FileQueryModifierParams $modifier = null): Query;
}
