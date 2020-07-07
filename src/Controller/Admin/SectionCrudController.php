<?php

namespace App\Controller\Admin;

use App\Admin\Field\WorkflowField;
use App\Entity\Section;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Workflow\Registry;

class SectionCrudController extends AbstractCrudController
{
    private $workflowRegistry;

    public function __construct(
        Registry $workflowRegistry
    ) {
        $this->workflowRegistry = $workflowRegistry;
    }

    public static function getEntityFqcn(): string
    {
        return Section::class;
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
            TextField::new('language'),
            IntegerField::new('position')
        ];
    }
}
