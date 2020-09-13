<?php

namespace App\Controller\Partial;

use App\Form\LanguageSwitcherFormType;
use App\Repository\Interfaces\SectionQueryingInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class LayoutController extends AbstractController
{
    private $sectionRepository;

    public function __construct(
        SectionQueryingInterface $sectionRepository
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

    public function renderLanguageSwitcher(): Response
    {
        $form = $this->createForm(LanguageSwitcherFormType::class);

        $response = $this->render('partial/layout/_language-switcher.html.twig', [
            'form' => $form->createView()
        ]);

        return $response;
    }
}
