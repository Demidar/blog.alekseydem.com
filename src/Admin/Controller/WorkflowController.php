<?php

namespace App\Admin\Controller;

use App\Entity\Article;
use App\Entity\Section;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Registry;
use Symfony\Contracts\Translation\TranslatorInterface;

class WorkflowController extends AbstractController
{
    private $workflowRegistry;
    private $entityManager;
    private $translator;

    private $entityMap = [
        'Article' => Article::class,
        'Section' => Section::class
    ];

    public function __construct(
        EntityManagerInterface $entityManager,
        Registry $workflowRegistry,
        TranslatorInterface $translator
    ) {
        $this->workflowRegistry = $workflowRegistry;
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    /**
     * @Route("/workflow", name="admin_workflow_apply", methods={"POST"})
     */
    public function applyWorkflow(Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException();
        }
        $id = $request->request->get('id');
        $entityName = $request->request->get('entityName');
        $transition = $request->request->get('transition');

        $entityFqcn = $this->entityMap[$entityName] ?? null;

        if (!$entityFqcn) {
            $this->addFlash('error', 'workflow.entity-is-not-whitelisted');
            return new RedirectResponse($request->headers->get('referer'));
        }

        $entity = $this->entityManager->getRepository($entityFqcn)->find($id);
        if (!$entity) {
            $this->addFlash('error', 'workflow.entity-not-found');
            return new RedirectResponse($request->headers->get('referer'));
        }

        $workflow = $this->workflowRegistry->get($entity);

        if (!$workflow->can($entity, $transition)) {
            $this->addFlash('error', 'workflow.transiton-not-allowed');
            return new RedirectResponse($request->headers->get('referer'));
        }

        $workflow->apply($entity, $transition);
        $this->entityManager->flush();

        $this->addFlash('message', 'workflow.transition-success');

        return new RedirectResponse($request->headers->get('referer'));
    }
}
