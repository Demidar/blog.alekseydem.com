<?php

namespace App\Admin\Form;

use App\Entity\Section;
use App\Repository\ModifierParams\SectionQueryModifierParams;
use App\Repository\SectionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SectionType extends AbstractType
{
    private $sectionRepository;

    public function __construct(SectionRepository $sectionRepository)
    {
        $this->sectionRepository = $sectionRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $section = $options['data'];

        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'label' => 'form.title.section'
            ])
            ->add('transition', WorkflowType::class, [
                'subject' => $section,
                'current_place' => $section->getStatus(),
            ])
            ->add('position', IntegerType::class, [
                'required' => false,
                'label' => 'form.position'
            ])
            ->add('slug', TextType::class, [
                'required' => false,
                'label' => 'form.slug'
            ])
            ->add('text', TextareaType::class, [
                'required' => false,
                'label' => 'form.text.section'
            ])
            ->add('parent', EntityType::class, [
                'required' => false,
                'label' => 'form.parent.section',
                'class' => Section::class,
                'choices' => $this->sectionRepository->findSections(new SectionQueryModifierParams([
                    'findExceptIds' => [$section->getId()]
                ])),
                'choice_value' => 'id',
                'choice_label' => 'title'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.submit'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Section::class,
        ]);
    }
}
