<?php

namespace App\Doctrine;

use App\Entity\Image;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class ImageSqlFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        $result = [];

        if ($this->hasParameter('status') && $targetEntity->getReflectionClass()->name === Image::class) {
            $condition = sprintf('%s.status = %s', $targetTableAlias, $this->getParameter('status'));
            $result[] = $condition;
        }

        return implode(' AND ', $result);
    }
}
