<?php

namespace App\Twig;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    private $liipCacheManager;
    private $uploadPath;

    public function __construct(
        CacheManager $liipCacheManager,
        string $uploadPath
    ) {
        $this->liipCacheManager = $liipCacheManager;
        $this->uploadPath = $uploadPath;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('uploaded_image_path', [$this, 'uploadedImagePath']),
            new TwigFilter('uploaded_file_path', [$this, 'uploadedFilePath']),
            new TwigFilter('imagine_filter_imagename', [$this, 'imagineFilterImagename']),
        ];
    }

    public function imagineFilterImagename(string $filename, string $filter, array $config = [], $resolver = null): string
    {
        $path = $this->uploadPath . '/' . $filename;
        return $this->liipCacheManager->getBrowserPath($path, $filter, $config, $resolver);
    }

    public function uploadedImagePath(string $filename): string
    {
        return $this->uploadPath . '/' . $filename;
    }

    public function uploadedFilePath(string $filename): string
    {
        return $this->uploadPath . '/' . $filename;
    }
}
