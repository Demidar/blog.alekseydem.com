<?php

namespace App\Entity;

use App\Repository\ImageReferenceArticleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImageReferenceArticleRepository::class)
 */
class ImageReferenceArticle extends ImageReference
{
    /**
     * @ORM\ManyToOne(targetEntity=Article::class, inversedBy="images")
     * @ORM\JoinColumn(nullable=false)
     */
    private $article;

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }
}
