<?php

namespace App\Repository\Filter;

class CommentFilter implements TranslatableFilter
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

    /**
     * @return string|null
     */
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * @param string|null $locale
     */
    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * @return bool|null
     */
    public function getFallback(): ?bool
    {
        return $this->fallback;
    }

    /**
     * @param bool|null $fallback
     */
    public function setFallback(?bool $fallback): void
    {
        $this->fallback = $fallback;
    }
}
