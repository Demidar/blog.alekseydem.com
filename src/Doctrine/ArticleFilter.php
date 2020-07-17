<?php

namespace App\Doctrine;

use App\Entity\Article;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class ArticleFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        $result = [];

        if ($this->hasParameter('status') && $targetEntity->getReflectionClass()->name === Article::class) {
            $condition = sprintf('%s.status = %s', $targetTableAlias, $this->getParameter('status'));
            if ($this->hasParameter('ownerId')) {
                $condition .= sprintf(' OR %s.owner_id = %s', $targetTableAlias, $this->getParameter('ownerId'));
            }
            $result[] = $condition;
        }

        return implode(' AND ', $result);
    }
}
