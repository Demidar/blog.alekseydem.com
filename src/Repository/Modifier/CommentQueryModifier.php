<?php

namespace App\Repository\Modifier;

class CommentQueryModifier
{
    /**
     * @var bool|null
     */
    public $withOwner;

    /**
     * @var bool|null
     */
    public $withParent;

    /**
     * @var bool|null
     */
    public $withArticle;

    /**
     * @var string|null
     */
    public $orderByField;

    /**
     * @var string|null
     */
    public $orderDirection;

    public function __construct(array $modifiersArray = null)
    {
        $this->withOwner = $modifiersArray['withOwner'] ?? null;
        $this->withParent = $modifiersArray['withParent'] ?? null;
        $this->withArticle = $modifiersArray['withArticle'] ?? null;
        $this->orderByField = $modifiersArray['orderByField'] ?? null;
        $this->orderDirection = $modifiersArray['orderDirection'] ?? null;
    }
}
