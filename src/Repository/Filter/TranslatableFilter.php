<?php

namespace App\Repository\Filter;

interface TranslatableFilter
{
    public function getLocale(): ?string;
    public function getFallback(): ?bool;
}
