<?php

namespace App\Repository\Modifier;

class ArticleQueryModifier
{
    /**
     * @var bool|null
     */
    public $withOwner;

    /**
     * @var bool|null
     */
    public $withSection;

    /**
     * @var bool|null
     */
    public $withComments;

    public function __construct(array $modifiersArray = null)
    {
        $this->withOwner = $modifiersArray['withOwner'] ?? null;
        $this->withSection = $modifiersArray['withSection'] ?? null;
        $this->withComments = $modifiersArray['withComments'] ?? null;
    }
}
