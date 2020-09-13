<?php

namespace App\Repository\Interfaces;


use App\Entity\ImageReference;
use App\Repository\ModifierParams\ImageReferenceQueryModifierParams;
use Doctrine\ORM\Query;

interface ImageReferenceQueryingInterface
{
    public function findImageReferenceById(int $id, ?ImageReferenceQueryModifierParams $modifier = null): ?ImageReference;

    /**
     * @return ImageReference[]
     */
    public function findImageReferences(?ImageReferenceQueryModifierParams $modifier = null): array;

    public function findImageReferencesQuery(?ImageReferenceQueryModifierParams $modifier = null): Query;
}
