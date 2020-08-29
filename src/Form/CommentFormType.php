<?php

namespace App\Form;

use App\Entity\Comment;
use App\Form\Transformer\ArticleToIdTransformer;
use App\Form\Transformer\CommentToIdTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentFormType extends AbstractType
{
    private $articleToIdTransformer;
    private $commentToIdTransformer;

    public function __construct(
        ArticleToIdTransformer $articleToIdTransformer,
        CommentToIdTransformer $commentToIdTransformer
    ) {
        $this->articleToIdTransformer = $articleToIdTransformer;
        $this->commentToIdTransformer = $commentToIdTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('article', HiddenType::class)
            ->add('parent', HiddenType::class, [
                'attr' => ['class' => 'js_comment-reply_form-parent']
            ])
            ->add('text', TextareaType::class, [
                'label' => 'form.a-comment'
            ])
            ->add('send', SubmitType::class, [
                'label' => 'form.send'
            ])
        ;

        $builder->get('article')->addModelTransformer($this->articleToIdTransformer);
        $builder->get('parent')->addModelTransformer($this->commentToIdTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
