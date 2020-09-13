<?php

namespace App\Controller;

use App\Exception\NotFoundException;
use App\Service\Section\SectionFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class SectionController extends AbstractController
{
    private $sectionFactory;

    public function __construct(
        SectionFactory $sectionFactory
    ) {
        $this->sectionFactory = $sectionFactory;
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/section/{slug}", name="section")
     */
    public function section($slug): Response
    {
        try {
            $sectionPage = $this->sectionFactory->createSectionPageBySlug($slug);
        } catch (NotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

        return $this->render('section/section.html.twig', [
            'section' => $sectionPage->section,
            'sectionChildren' => $sectionPage->children,
            'breadcrumbs' => $sectionPage->breadcrumbs
        ]);
    }
}
