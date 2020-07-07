<?php

namespace App\Controller;

use App\Entity\Section;
use App\Repository\SectionRepository;
use App\Service\Breadcrumbs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SectionController extends AbstractController
{
    private $sectionRepository;
    private $breadcrumbs;

    public function __construct(
        SectionRepository $sectionRepository,
        Breadcrumbs $breadcrumbs
    ) {
        $this->sectionRepository = $sectionRepository;
        $this->breadcrumbs = $breadcrumbs;
    }

    /**
     * @Route("/section/{slug}", name="section")
     */
    public function section($slug)
    {
        $section = $this->sectionRepository->findPublic($slug);
        if (!$section) {
            return new Response(null, 404);
        }

        $breadcrumbs = $this->breadcrumbs->getBreadcrumbsForSection($section);

        return $this->render('section/section.html.twig', [
            'section' => $section,
            'breadcrumbs' => $breadcrumbs
        ]);
    }
}
