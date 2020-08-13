<?php

namespace App\Admin\Controller;

use App\Entity\Section;
use App\Admin\Form\SectionType;
use App\Repository\Filter\SectionFilter;
use App\Repository\Modifier\SectionQueryModifier;
use App\Repository\SectionRepository;
use App\Service\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/section")
 */
class SectionCrudController extends AbstractCrudController
{
    private $sectionRepository;
    private $entityManager;
    private $paginator;

    public function __construct(
        SectionRepository $sectionRepository,
        EntityManagerInterface $entityManager,
        PaginatorInterface $paginator
    ) {
        $this->sectionRepository = $sectionRepository;
        $this->entityManager = $entityManager;
        $this->paginator = $paginator;
    }

    /**
     * @Route("/index", name="admin_section_index")
     */
    public function index(Request $request)
    {
        $lang = $request->query->get('lang', $request->getLocale());
        $page = $request->query->getInt('page', 1);

        $sectionsQuery = $this->sectionRepository->findSectionsQuery(new SectionFilter([
            'locale' => $lang,
            'fallback' => true
        ]), new SectionQueryModifier([
            'withParent' => true
        ]));

        $paginatedSections = $this->paginator->paginate(
            $sectionsQuery,
            $page,
            20
        );

        return $this->render('admin/crud/section/index.html.twig', [
            'paginatedSections' => $paginatedSections
        ]);
    }

    /**
     * @Route("/new", name="admin_section_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $lang = $request->query->get('lang', $request->getLocale());

        $section = new Section();
        $section->setTranslatableLocale($lang);

        $form = $this->createForm(SectionType::class, $section);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($section);
            $entityManager->flush();

            return $this->redirectToRoute('admin_section_index');
        }

        return $this->render('admin/crud/section/new.html.twig', [
            'section' => $section,
            'form' => $form->createView(),
        ]);
    }

//    /**
//     * @Route("/show/{id}", name="admin_section_show", methods={"GET"})
//     */
//    public function show(int $id, Request $request): Response
//    {
//        $lang = $request->query->get('lang', $request->getLocale());
//
//        $section = $this->sectionRepository->findSectionById($id, new SectionFilter(['locale' => $lang, 'fallback' => true]));
//        if (!$section) {
//            throw new NotFoundHttpException('section not found');
//        }
//
//        return $this->render('admin/crud/section/show.html.twig', [
//            'section' => $section,
//        ]);
//    }

    /**
     * @Route("/edit/{id}", name="admin_section_edit", methods={"GET","POST"})
     */
    public function edit(int $id, Request $request): Response
    {
        $lang = $request->query->get('lang', $request->getLocale());

        $section = $this->sectionRepository->findSectionById($id, new SectionFilter(['locale' => $lang, 'fallback' => false]));
        if (!$section) {
            throw new NotFoundHttpException('section not found');
        }
        $section->setTranslatableLocale($lang);

        $form = $this->createForm(SectionType::class, $section);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_section_edit', ['id' => $id]);
        }

        return $this->render('admin/crud/section/edit.html.twig', [
            'section' => $section,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="admin_section_delete", methods={"POST"})
     */
    public function delete(Request $request, Section $section): Response
    {
        if ($this->isCsrfTokenValid('delete'.$section->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($section);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_section_index');
    }
}
