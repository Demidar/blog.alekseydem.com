<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * TODO
 */
class ImageSelectorType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'multiple' => false,
            'expanded' => false
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {

    }

    public function getBlockPrefix()
    {
        return 'imageSelector';
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
