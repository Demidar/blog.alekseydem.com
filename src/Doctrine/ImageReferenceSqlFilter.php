<?php

namespace App\Doctrine;

use App\Entity\ImageReference;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class ImageReferenceSqlFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        $result = [];

        if ($this->hasParameter('status') && $targetEntity->getReflectionClass()->name === ImageReference::class) {
            $condition = sprintf('%s.status = %s', $targetTableAlias, $this->getParameter('status'));
            $result[] = $condition;
        }

        return implode(' AND ', $result);
    }
}
