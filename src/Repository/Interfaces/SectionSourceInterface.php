<?php

namespace App\Repository\Interfaces;


use App\Entity\Section;
use App\Repository\ModifierParams\SectionQueryModifierParams;
use Doctrine\ORM\Query;

interface SectionSourceInterface
{
    /**
     * parent::getPath() with applying translation hints
     *
     * @return Section[]
     */
    public function getSectionPath(Section $section, ?SectionQueryModifierParams $modifier = null): array;

    public function findSectionById(int $id, ?SectionQueryModifierParams $modifier = null): ?Section;

    public function findSectionBySlug(string $slug, ?SectionQueryModifierParams $modifier = null): ?Section;

    /**
     * @return Section[]
     */
    public function findSections(?SectionQueryModifierParams $modifier = null): array;

    public function findSectionsQuery(?SectionQueryModifierParams $modifier = null): Query;

    /**
     * @return Section[]
     */
    public function getRootSections(?SectionQueryModifierParams $modifier = null): array;
}
