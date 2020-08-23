<?php

namespace App\Doctrine;

use App\Entity\File;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class FileSqlFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        $result = [];

        if ($this->hasParameter('status') && $targetEntity->getReflectionClass()->name === File::class) {
            $condition = sprintf('%s.status = %s', $targetTableAlias, $this->getParameter('status'));
            $result[] = $condition;
        }

        return implode(' AND ', $result);
    }
}
