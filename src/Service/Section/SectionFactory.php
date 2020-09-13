<?php

namespace App\Service\Section;

use App\Exception\NotFoundException;
use App\Model\SectionPage;
use App\Repository\Interfaces\SectionQueryingInterface;
use App\Repository\ModifierParams\ArticleQueryModifierParams;
use App\Repository\ModifierParams\SectionQueryModifierParams;
use App\Service\Breadcrumbs;
use Symfony\Contracts\Translation\TranslatorInterface;

class SectionFactory
{
    private SectionQueryingInterface $sectionRepository;
    private Breadcrumbs $breadcrumbs;
    private TranslatorInterface $translator;

    public function __construct(
        SectionQueryingInterface $sectionRepository,
        Breadcrumbs $breadcrumbs,
        TranslatorInterface $translator
    ) {
        $this->sectionRepository = $sectionRepository;
        $this->breadcrumbs = $breadcrumbs;
        $this->translator = $translator;
    }
    public function createSectionPageBySlug(string $slug): SectionPage
    {
        $section = $this->sectionRepository->findSectionBySlug($slug, new SectionQueryModifierParams([
            'withArticles' => true,
            'articles' => new ArticleQueryModifierParams([
                'withImages' => true
            ])
        ]));
        if (!$section) {
            throw new NotFoundException(sprintf($this->translator->trans('section.slug-not-found', [
                'slug' => $slug
            ])));
        }

        $sectionChildren = $this->sectionRepository->findChildren($section->getId());

        $breadcrumbs = $this->breadcrumbs->getBreadcrumbsForSection($section);

        $model = new SectionPage();
        $model->section = $section;
        $model->children = $sectionChildren;
        $model->breadcrumbs = $breadcrumbs;

        return $model;
    }
}
