<?php

namespace App\Entity;

use App\Repository\FileReferenceArticleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FileReferenceArticleRepository::class)
 */
class FileReferenceArticle extends FileReference
{
    /**
     * @ORM\ManyToOne(targetEntity=Article::class, inversedBy="files")
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
