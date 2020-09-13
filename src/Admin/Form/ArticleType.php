<?php

namespace App\Admin\Form;

use App\Entity\Article;
use App\Entity\Section;
use App\Entity\User;
use App\Repository\Interfaces\SectionQueryingInterface;
use App\Repository\Interfaces\UserQueryingInterface;
use App\Repository\SectionRepository;
use App\Repository\UserRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    private $sectionRepository;
    private $userRepository;

    public function __construct(
        SectionQueryingInterface $sectionRepository,
        UserQueryingInterface $userRepository
    ) {
        $this->sectionRepository = $sectionRepository;
        $this->userRepository = $userRepository;
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
            ->add('text', CKEditorType::class, [
                'required' => true,
                'label' => 'form.text.article',
                'config' => [
                    'filebrowserImageUploadRoute' => 'upload-article-images',
                    'filebrowserImageUploadRouteParameters' => ['id' => $article->getId()],
                ]
            ])
            ->add('section', EntityType::class, [
                'required' => false,
                'label' => 'form.attached-section',
                'class' => Section::class,
                'choices' => $this->sectionRepository->findSections(),
                'choice_value' => 'id',
                'choice_label' => 'title',
            ])
            ->add('owner', EntityType::class, [
                'required' => false,
                'label' => 'form.owner.article',
                'class' => User::class,
                'choices' => $this->userRepository->findUsers(),
                'choice_value' => static function (?User $entity) {
                    return $entity ? $entity->getId() : '';
                },
                'choice_name' => static function (?User $entity) {
                    return $entity ? $entity->getUsername() : '';
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
