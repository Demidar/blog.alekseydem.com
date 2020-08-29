<?php

namespace App\Repository\ModifierParams;

interface TranslatableQueryModifierParams
{
    public function getLocale(): ?string;
    public function getFallback(): ?bool;
}
