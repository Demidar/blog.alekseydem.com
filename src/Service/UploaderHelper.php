<?php

namespace App\Service;

use App\Entity\AbstractFile as AbstractFileEntity;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    private $uploadDir;
    private $filesystem;

    public function __construct(string $uploadDir, Filesystem $filesystem)
    {
        $this->uploadDir = $uploadDir;
        $this->filesystem = $filesystem;
    }

    public function uploadFile(File $file, AbstractFileEntity $entity): void
    {
        $newFilename = uniqid('', true) . '.' . $this->getExtension($file);

        $movedFile = $file->move($this->uploadDir, $newFilename);

        $entity->setOriginalName($this->getFilename($file));
        $entity->setMimeType($this->getMimeType($file));
        $entity->setName($movedFile->getFilename());
        $entity->setSize($movedFile->getSize());
    }

    public function deleteFile(string $filename): bool
    {
        $file = new File($this->uploadDir . '/' . $filename);
        if (!$file) {
            return false;
        }

        $this->filesystem->remove($file->getPathname());

        return true;
    }

    private function getFilename(File $file): string
    {
        if ($file instanceof UploadedFile) {
            return $file->getClientOriginalName();
        }
        return $file->getFilename();
    }

    private function getExtension(File $file): string
    {
        if ($file instanceof UploadedFile) {
            return $file->getClientOriginalExtension();
        }
        return $file->getExtension();
    }

    private function getMimeType(File $file): string
    {
        if ($file instanceof UploadedFile) {
            return $file->getClientMimeType();
        }
        return $file->getMimeType();
    }
}
