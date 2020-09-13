<?php

namespace App\Repository\Interfaces;


use App\Entity\Article;
use App\Repository\ModifierParams\ArticleQueryModifierParams;
use Doctrine\ORM\Query;

interface ArticleSourceInterface
{
    public function findArticleBySlug(string $slug, ?ArticleQueryModifierParams $modifierParams = null): ?Article;

    public function findArticleById(string $id, ?ArticleQueryModifierParams $modifierParams = null): ?Article;

    /**
     * @return Article[]
     */
    public function findArticles(?ArticleQueryModifierParams $modifierParams = null): array;

    public function getArticlesQuery(?ArticleQueryModifierParams $modifierParams = null): Query;

    /**
     * @return Article[]
     */
    public function findArticlesBySection(int $sectionId, ?ArticleQueryModifierParams $modifierParams = null): array;
}
