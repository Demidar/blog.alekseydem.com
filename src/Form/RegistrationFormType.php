<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'form.username',
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => 'form.constraint.common.too-small'
                    ]),
                    new NotBlank([
                        'message' => 'form.constraint.common.is-blank'
                    ])
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'invalid_message' => 'form.constraint.password.is-not-equal',
                'constraints' => [
                    new NotBlank([
                        'message' => 'form.constraint.common.is-blank',
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'form.constraint.common.too-small',
                        'max' => 100,
                        'maxMessage' => 'form.constraint.common.too-big',
                    ]),
                ],
                'first_options' => ['label' => 'form.password', ],
                'second_options' => ['label' => 'form.repeat-password']
            ])
//            ->add('agreeTerms', CheckboxType::class, [
//                'mapped' => false,
//                'constraints' => [
//                    new IsTrue([
//                        'message' => 'You should agree to our terms.',
//                    ]),
//                ],
//            ])
            ->add('register', SubmitType::class, [
                'label' => 'form.register'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'validation_groups' => 'registration',
            'data_class' => User::class,
        ]);
    }
}
