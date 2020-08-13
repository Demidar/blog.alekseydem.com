<?php

namespace App\Repository\Modifier;

class SectionQueryModifier
{
    /**
     * @var bool|null
     */
    public $withParent;

    public function __construct(array $modifiersArray = null)
    {
        $this->withParent = $modifiersArray['withParent'] ?? null;
    }
}
