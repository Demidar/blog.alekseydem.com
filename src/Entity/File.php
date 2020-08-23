<?php

namespace App\Entity;

use App\Repository\FileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FileRepository::class)
 */
class File extends AbstractFile
{
    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="files")
     */
    private $owner;

    /**
     * @ORM\OneToMany(targetEntity=FileReference::class, mappedBy="file", orphanRemoval=true)
     */
    private $fileReferences;

    public function __construct()
    {
        $this->fileReferences = new ArrayCollection();
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
     * @return Collection|FileReference[]
     */
    public function getFileReferences(): Collection
    {
        return $this->fileReferences;
    }

    public function addFileReference(FileReference $fileReference): self
    {
        if (!$this->fileReferences->contains($fileReference)) {
            $this->fileReferences[] = $fileReference;
            $fileReference->setFile($this);
        }

        return $this;
    }

    public function removeFileReference(FileReference $fileReference): self
    {
        if ($this->fileReferences->contains($fileReference)) {
            $this->fileReferences->removeElement($fileReference);
            // set the owning side to null (unless already changed)
            if ($fileReference->getFile() === $this) {
                $fileReference->setFile(null);
            }
        }

        return $this;
    }
}
