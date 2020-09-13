<?php

namespace App\Service;

use App\Entity\Comment;
use App\Model\CommentsHierarchy;

class HierarchyBuilder
{
    /**
     * @param Comment[] $comments
     * @return CommentsHierarchy[]
     */
    public function buildCommentsHierarchy($comments, $parentCommentId = null): array
    {
        $result = [];
        foreach ($comments as $comment) {
            $parent = $comment->getParent();
            if (
                ($parent === null && $parentCommentId === null) ||
                ($parent && $parent->getId() === $parentCommentId)
            ) {
                $ch = new CommentsHierarchy();
                $ch->item = $comment;
                $ch->children = $this->buildCommentsHierarchy($comments, $comment->getId());
                // $result[$comment->getId()] = $ch;
                $result[] = $ch;
            }
        }

        return $result;
    }
}
