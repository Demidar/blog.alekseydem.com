<?php

namespace App\Controller\Partial;

use App\Repository\SectionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class LayoutController extends AbstractController
{
    private $sectionRepository;

    public function __construct(
        SectionRepository $sectionRepository
    ) {
        $this->sectionRepository = $sectionRepository;
    }

    public function renderHeaderNav(): Response
    {
        $sections = $this->sectionRepository->getRootSections();

        return $this->render('partial/layout/_header-nav.html.twig', [
            'sections' => $sections
        ]);
    }
}
