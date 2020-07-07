<?php

namespace App\Service;

use App\Entity\Section;
use App\Model\Link;
use App\Repository\SectionRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Breadcrumbs
{
    private $urlGenerator;
    private $translator;
    private $sectionRepository;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        TranslatorInterface $translator,
        SectionRepository $sectionRepository
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->sectionRepository = $sectionRepository;
    }

    /**
     * @param Section $section
     * @return Link[]
     */
    public function getBreadcrumbsForSection(Section $section): array
    {
        /** @var Section[] $sections */
        $sections = $this->sectionRepository->getPath($section);

        $breadcrumbs = array_map(function ($section) {
            /** @var Section $section */
            $link = new Link();
            $link->title = $section->getTitle();
            $link->url = $this->urlGenerator->generate('section', ['slug' => $section->getSlug()]);
            return $link;
        }, $sections);

        array_unshift($breadcrumbs, $this->getHomepageLink());

        return $breadcrumbs;
    }

    private function getHomepageLink()
    {
        $link = new Link();
        $link->url = $this->urlGenerator->generate('homepage');
        $link->title = $this->translator->trans('homepage');
        return $link;
    }
}
