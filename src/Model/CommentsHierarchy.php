<?php

namespace App\Model;

use App\Entity\Comment;

class CommentsHierarchy
{
    /**
     * @var Comment
     */
    public $item;
    /**
     * @var CommentsHierarchy[]
     */
    public $children;
}
