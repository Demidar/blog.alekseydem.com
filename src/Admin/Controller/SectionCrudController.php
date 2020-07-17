<?php

namespace App\Admin\Controller;

use App\Repository\SectionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SectionCrudController extends AbstractCrudController
{
    private $sectionRepository;

    public function __construct(SectionRepository $sectionRepository)
    {
        $this->sectionRepository = $sectionRepository;
    }

    /**
     * @Route("/section/list", name="admin_section_list")
     */
    public function list(Request $request)
    {
        $this->denyAccessUnlessGranted();
        $lang = $request->query->get('lang', 'en');
        $sections = $this->sectionRepository->findAll();
        return $this->render('admin/crud/section/list.html.twig', $sections);
    }
}
