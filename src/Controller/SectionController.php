<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
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
        $section = $this->sectionRepository->findPublic($slug);
        if (!$section) {
            throw new NotFoundHttpException();
        }

        $sectionChildren = $this->sectionRepository->findChildrenPublic($section->getId());

        $articles = $this->articleRepository->findAllPublishedBySection($section->getId());

        $breadcrumbs = $this->breadcrumbs->getBreadcrumbsForSection($section);

        return $this->render('section/section.html.twig', [
            'section' => $section,
            'sectionChildren' => $sectionChildren,
            'articles' => $articles,
            'breadcrumbs' => $breadcrumbs
        ]);
    }
}
