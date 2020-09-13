<?php

namespace App\Form\Transformer;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\Interfaces\ArticleQueryingInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ArticleToIdTransformer implements DataTransformerInterface
{
    private $articleRepository;

    public function __construct(
        ArticleQueryingInterface $articleRepository
    ) {
        $this->articleRepository = $articleRepository;
    }

    /**
     * @param Article $article
     * @return int|null
     */
    public function transform($article): ?int
    {
        if (!$article) {
            return null;
        }

        return $article->getId();
    }

    public function reverseTransform($id): ?Article
    {
        if (!$id) {
            return null;
        }

        $article = $this->articleRepository->findArticleById($id);

        if (!$article) {
            throw new TransformationFailedException('This article does not exist!');
        }

        return $article;
    }
}
