<?php

namespace App\Doctrine;

use App\Entity\FileReference;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class FileReferenceSqlFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        $result = [];

        if ($this->hasParameter('status') && $targetEntity->getReflectionClass()->name === FileReference::class) {
            $condition = sprintf('%s.status = %s', $targetTableAlias, $this->getParameter('status'));
            $result[] = $condition;
        }

        return implode(' AND ', $result);
    }
}
