<?php

namespace App\Repository\Interfaces;


use App\Entity\Image;
use App\Repository\ModifierParams\ImageQueryModifierParams;
use Doctrine\ORM\Query;

interface ImageQueryingInterface
{
    public function findImageById(int $id, ?ImageQueryModifierParams $modifier = null): ?Image;

    /**
     * @return Image[]
     */
    public function findImages(?ImageQueryModifierParams $modifier = null): array;

    public function findImagesQuery(?ImageQueryModifierParams $modifier = null): Query;
}
