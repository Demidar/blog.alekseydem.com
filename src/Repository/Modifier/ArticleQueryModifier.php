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

    /**
     * @var string|null
     */
    public $orderByField;

    /**
     * @var string|null
     */
    public $orderDirection;

    /**
     * @var string[]|null
     */
    public $select;

    /**
     * @var CommentQueryModifier|null
     */
    public $comments;

    public function __construct(array $modifiersArray = null)
    {
        $this->withOwner = $modifiersArray['withOwner'] ?? null;
        $this->withSection = $modifiersArray['withSection'] ?? null;
        $this->withComments = $modifiersArray['withComments'] ?? null;
        $this->orderByField = $modifiersArray['orderByField'] ?? null;
        $this->orderDirection = $modifiersArray['orderDirection'] ?? null;
        $this->select = $modifiersArray['select'] ?? null;
        $this->comments = $modifiersArray['comments'] ?? null;
    }
}
