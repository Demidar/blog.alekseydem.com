<?php

namespace App\Controller\Admin;

use App\Admin\Field\WorkflowField;
use App\Entity\Section;
use App\Form\Admin\SectionFormType;
use App\Repository\SectionRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Workflow\Registry;

class SectionCrudController extends AbstractCrudController
{
    private $workflowRegistry;
    private $sectionRepository;

    public function __construct(
        Registry $workflowRegistry,
        SectionRepository $sectionRepository
    ) {
        $this->workflowRegistry = $workflowRegistry;
        $this->sectionRepository = $sectionRepository;
    }

    public static function getEntityFqcn(): string
    {
        return Section::class;
    }

    public function editRus(AdminContext $context)
    {
        /** @var Section $instance */
        $simpleInstance = $context->getEntity()->getInstance();
        $section = $this->sectionRepository->findForAdmin($simpleInstance->getId(), 'ru');

        $editForm = $this->createForm(SectionFormType::class, $section);

        $responseParameters = $this->configureResponseParameters(KeyValueStore::new([
            'pageName' => Crud::PAGE_EDIT,
            'templatePath' => 'admin/crud/edit-lang.html.twig',
            'edit_form' => $editForm,
            'entity' => $context->getEntity(),
        ]));

        $event = new AfterCrudActionEvent($context, $responseParameters);
        $this->get('event_dispatcher')->dispatch($event);
        if ($event->isPropagationStopped()) {
            return $event->getResponse();
        }

        return $responseParameters;
    }

    public function configureActions(Actions $actions): Actions
    {
        $editRus = Action::new('section-edit-rus', 'Edit Rus', 'fa fa-file-invoice')
            ->linkToCrudAction('editRus');

        return $actions
            ->add(Crud::PAGE_INDEX, $editRus)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('parent'),
            TextField::new('title'),
            WorkflowField::new('status')->hideOnForm(),
            TextEditorField::new('text'),
            SlugField::new('slug')->setTargetFieldName('title')->setRequired(false),
            TextField::new('locale'),
            IntegerField::new('position')
        ];
    }
}
