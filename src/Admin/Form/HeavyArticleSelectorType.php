<?php

namespace App\Admin\Form;

use App\Admin\Form\Transformer\ArticleToObjectTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HeavyArticleSelectorType extends AbstractType
{
    private $articleToObjectTransformer;

    public function __construct(ArticleToObjectTransformer $articleToObjectTransformer)
    {
        $this->articleToObjectTransformer = $articleToObjectTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->articleToObjectTransformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'invalid_message' => 'The selected article does not exist',
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
