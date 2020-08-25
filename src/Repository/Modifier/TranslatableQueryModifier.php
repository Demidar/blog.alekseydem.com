<?php

namespace App\Repository\Modifier;

interface TranslatableQueryModifier
{
    public function getLocale(): ?string;
    public function getFallback(): ?bool;
}
