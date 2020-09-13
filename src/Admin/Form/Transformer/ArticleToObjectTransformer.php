<?php

namespace App\Admin\Form\Transformer;

use App\Entity\Article;
use App\Repository\ArticleQuerying;
use App\Repository\Interfaces\ArticleQueryingInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ArticleToObjectTransformer implements DataTransformerInterface
{
    private $articleRepository;

    public function __construct(
        ArticleQueryingInterface $articleRepository
    ) {
        $this->articleRepository = $articleRepository;
    }
    /**
     * @param object $article
     * @return int|null
     */
    public function transform($article): object
    {
        return $article;
    }

    public function reverseTransform($object): ?Article
    {
        if (!$object || !$object->id) {
            return null;
        }

        $article = $this->articleRepository->findArticleById($object->id);

        if (!$article) {
            throw new TransformationFailedException('This article does not exists!');
        }

        return $article;
    }
}
