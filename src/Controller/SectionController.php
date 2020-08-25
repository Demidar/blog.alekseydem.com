<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\Modifier\ArticleQueryModifier;
use App\Repository\Modifier\SectionQueryModifier;
use App\Repository\SectionRepository;
use App\Service\Breadcrumbs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class SectionController extends AbstractController
{
    private $sectionRepository;
    private $breadcrumbs;
    private $articleRepository;

    public function __construct(
        SectionRepository $sectionRepository,
        ArticleRepository $articleRepository,
        Breadcrumbs $breadcrumbs
    ) {
        $this->sectionRepository = $sectionRepository;
        $this->breadcrumbs = $breadcrumbs;
        $this->articleRepository = $articleRepository;
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/section/{slug}", name="section")
     */
    public function section($slug)
    {
        $section = $this->sectionRepository->findSectionBySlug($slug, new SectionQueryModifier([
            'withArticles' => true,
            'articles' => new ArticleQueryModifier([
                'withImages' => true
            ])
        ]));
        if (!$section) {
            throw new NotFoundHttpException();
        }

        $sectionChildren = $this->sectionRepository->findChildren($section->getId());

        $breadcrumbs = $this->breadcrumbs->getBreadcrumbsForSection($section);

        return $this->render('section/section.html.twig', [
            'section' => $section,
            'sectionChildren' => $sectionChildren,
            'breadcrumbs' => $breadcrumbs
        ]);
    }
}
