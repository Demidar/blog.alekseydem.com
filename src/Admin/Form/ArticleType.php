<?php

namespace App\Admin\Form;

use App\Entity\Article;
use App\Entity\Section;
use App\Repository\SectionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    private $sectionRepository;

    public function __construct(SectionRepository $sectionRepository)
    {
        $this->sectionRepository = $sectionRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $article = $options['data'];

        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'label' => 'form.title.article'
            ])
            ->add('transition', WorkflowType::class, [
                'subject' => $article,
                'current_place' => $article->getStatus(),
            ])
            ->add('slug', TextType::class, [
                'required' => false,
                'label' => 'form.slug'
            ])
            ->add('text', TextareaType::class, [
                'required' => false,
                'label' => 'form.text.article'
            ])
            ->add('section', EntityType::class, [
                'required' => false,
                'label' => 'form.attached-section',
                'class' => Section::class,
                'choices' => $this->sectionRepository->findSections(),
                'choice_value' => static function (?Section $entity) {
                    return $entity ? $entity->getId() : '';
                },
                'choice_name' => static function (?Section $entity) {
                    return $entity ? $entity->getTitle() : '';
                }
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.submit'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
