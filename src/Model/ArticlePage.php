<?php

namespace App\Model;

use App\Entity\Article;
use Symfony\Component\Form\FormView;

class ArticlePage
{
    public Article $article;
    /** @var Article[] */
    public array $attachedArticles = [];
    /** @var CommentsHierarchy[] */
    public array $comments = [];
    /** @var Link[] */
    public array $breadcrumbs = [];
    public ?FormView $createCommentFormView;
}
