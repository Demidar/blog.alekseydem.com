<?php

namespace App\Repository\Interfaces;


use App\Entity\FileReference;
use App\Repository\ModifierParams\FileReferenceQueryModifierParams;
use Doctrine\ORM\Query;

interface FileReferenceSourceInterface
{
    public function findFileReferenceById(int $id, ?FileReferenceQueryModifierParams $modifier = null): ?FileReference;

    /**
     * @return FileReference[]
     */
    public function findFileReferences(?FileReferenceQueryModifierParams $modifier = null): array;

    public function findFileReferencesQuery(?FileReferenceQueryModifierParams $modifier = null): Query;
}
