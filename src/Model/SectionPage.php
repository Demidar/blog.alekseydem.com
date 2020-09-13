<?php

namespace App\Model;

use App\Entity\Section;

class SectionPage
{
    public Section $section;
    /** @var Section[] */
    public array $children;
    /** @var Link[] */
    public array $breadcrumbs;
}
