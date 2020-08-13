<?php

namespace App\Repository\Filter;

class ArticleFilter
{
    /**
     * @var bool
     */
    public $fallback;

    /**
     * @var string
     */
    public $locale;

    public function __construct(array $filterArray = null)
    {
        $this->fallback = $filterArray['fallback'] ?? null;
        $this->locale = $filterArray['locale'] ?? null;
    }
}
