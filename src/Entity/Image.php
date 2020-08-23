<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 */
class Image extends AbstractFile
{
    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="images")
     */
    private $owner;

    /**
     * @ORM\OneToMany(targetEntity=ImageReference::class, mappedBy="image", orphanRemoval=true)
     */
    private $imageReferences;

    public function __construct()
    {
        $this->imageReferences = new ArrayCollection();
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection|ImageReference[]
     */
    public function getImageReferences(): Collection
    {
        return $this->imageReferences;
    }

    public function addImageReference(ImageReference $imageReference): self
    {
        if (!$this->imageReferences->contains($imageReference)) {
            $this->imageReferences[] = $imageReference;
            $imageReference->setImage($this);
        }

        return $this;
    }

    public function removeImageReference(ImageReference $imageReference): self
    {
        if ($this->imageReferences->contains($imageReference)) {
            $this->imageReferences->removeElement($imageReference);
            // set the owning side to null (unless already changed)
            if ($imageReference->getImage() === $this) {
                $imageReference->setImage(null);
            }
        }

        return $this;
    }
}
