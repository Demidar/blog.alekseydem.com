<?php

namespace App\Entity\Closure;

use App\Entity\CloneableEntityTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Tree\Entity\MappedSuperclass\AbstractClosure;

/**
 * @ORM\Entity()
 */
class SectionClosure extends AbstractClosure
{
    use CloneableEntityTrait;
}
