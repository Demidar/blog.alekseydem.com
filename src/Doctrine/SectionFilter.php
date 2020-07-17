<?php

namespace App\Doctrine;

use App\Entity\Section;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class SectionFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        $result = [];

        if ($this->hasParameter('status') && $targetEntity->getReflectionClass()->name === Section::class) {
            $result[] = sprintf('%s.status = %s', $targetTableAlias, $this->getParameter('status'));
        }

        return implode(' AND ', $result);
    }
}
