<?php

namespace App\Repository\Filter;

class SectionFilter implements TranslatableFilter
{
    /**
     * @var bool|null
     */
    private $fallback;

    /**
     * @var string|null
     */
    private $locale;

    public function __construct(array $filterArray = null)
    {
        $this->setFallback($filterArray['fallback'] ?? null);
        $this->setLocale($filterArray['locale'] ?? null);
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }

    public function getFallback(): ?bool
    {
        return $this->fallback;
    }

    public function setFallback(?bool $fallback): void
    {
        $this->fallback = $fallback;
    }
}
