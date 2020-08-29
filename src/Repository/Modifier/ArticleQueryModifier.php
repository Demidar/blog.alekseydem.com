<?php

namespace App\Repository\Modifier;

use App\Repository\ModifierParams\ArticleQueryModifierParams;
use Doctrine\ORM\QueryBuilder;

class ArticleQueryModifier
{
    private $commentQueryModifier;

    public function __construct(CommentQueryModifier $commentQueryModifier)
    {
        $this->commentQueryModifier = $commentQueryModifier;
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
            $qb->addSelect('a_section')
                ->leftJoin("$alias.section", 'a_section');
        }
        if ($modifier->withOwner) {
            $qb->addSelect('a_owner')
                ->leftJoin("$alias.owner", 'a_owner');
        }
        if ($modifier->withComments) {
            $qb->addSelect('a_comments')
                ->leftJoin("$alias.comments", 'a_comments');
        }
        if ($modifier->withImages) {
            $qb->addSelect(['a_images', 'a_image_references'])
                ->leftJoin("$alias.images", 'a_image_references')
                ->leftJoin('a_image_references.image', 'a_images');
        }
        if ($modifier->limit) {
            $qb->setMaxResults($modifier->limit);
        }
        if ($modifier->comments) {
            $this->commentQueryModifier->applyModifier($qb, $modifier->comments, 'a_comments');
        }
    }
}
