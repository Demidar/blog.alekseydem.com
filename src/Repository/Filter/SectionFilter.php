<?php

namespace App\Repository\Filter;

class SectionFilter
{
    /**
     * @var bool|null
     */
    public $fallback;

    /**
     * @var string|null
     */
    public $locale;

    public function __construct(array $filterArray = null)
    {
        $this->fallback = $filterArray['fallback'] ?? null;
        $this->locale = $filterArray['locale'] ?? null;
    }
}
