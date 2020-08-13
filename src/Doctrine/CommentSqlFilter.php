<?php

namespace App\Doctrine;

use App\Entity\Comment;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class CommentSqlFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        $result = [];

        if ($this->hasParameter('status') && $targetEntity->getReflectionClass()->name === Comment::class) {
            $condition = sprintf('%s.status = %s', $targetTableAlias, $this->getParameter('status'));
            if ($this->hasParameter('ownerId')) {
                $condition .= sprintf(' OR %s.owner_id = %s', $targetTableAlias, $this->getParameter('ownerId'));
            }
            $result[] = $condition;
        }

        return implode(' AND ', $result);
    }
}
