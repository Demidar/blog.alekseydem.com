<?php

namespace App\Repository\Modifier;

use App\Repository\ModifierParams\ArticleQueryModifierParams;
use Doctrine\ORM\QueryBuilder;

class ArticleQueryModifier
{
    private $commentQueryModifier;
    private $userQueryModifier;
    private $sectionQueryModifier;

    public function __construct(
        CommentQueryModifier $commentQueryModifier,
        UserQueryModifier $userQueryModifier,
        SectionQueryModifier $sectionQueryModifier
    ) {
        $this->commentQueryModifier = $commentQueryModifier;
        $this->userQueryModifier = $userQueryModifier;
        $this->sectionQueryModifier = $sectionQueryModifier;
    }

    public function applyModifier(QueryBuilder $qb, ?ArticleQueryModifierParams $modifier, string $alias): void
    {
        if (!$modifier) {
            return;
        }

        if ($modifier->select) {
            $qb->select(
                array_map(static function ($field) use ($alias) {
                    return "$alias.$field";
                }, $modifier->select)
            );
        }
        if ($modifier->orderByField) {
            $qb->addOrderBy("$alias.{$modifier->orderByField}", $modifier->orderDirection ?: 'ASC');
        }
        if ($modifier->withSection) {
            $qb->addSelect("{$alias}_section")
                ->leftJoin("$alias.section", "{$alias}_section");
        }
        if ($modifier->withOwner) {
            $qb->addSelect("{$alias}_owner")
                ->leftJoin("$alias.owner", "{$alias}_owner");
        }
        if ($modifier->withComments) {
            $qb->addSelect("{$alias}_comments")
                ->leftJoin("$alias.comments", "{$alias}_comments");
        }
        if ($modifier->withImages) {
            $qb->addSelect(["{$alias}_images", "{$alias}_image_references"])
                ->leftJoin("$alias.images", "{$alias}_image_references")
                ->leftJoin("{$alias}_image_references.image", "{$alias}_images");
        }
        if ($modifier->limit) {
            $qb->setMaxResults($modifier->limit);
        }
        if ($modifier->comments) {
            $this->commentQueryModifier->applyModifier($qb, $modifier->comments, "{$alias}_comments");
        }
        if ($modifier->owner) {
            $this->userQueryModifier->applyModifier($qb, $modifier->owner, "{$alias}_owner");
        }
        if ($modifier->section) {
            $this->sectionQueryModifier->applyModifier($qb, $modifier->section, "{$alias}_section");
        }
    }
}
