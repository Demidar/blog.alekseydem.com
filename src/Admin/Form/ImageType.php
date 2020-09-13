<?php

namespace App\Admin\Form;

use App\Entity\Image;
use App\Entity\User;
use App\Repository\Interfaces\UserSourceInterface;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageType extends AbstractType
{
    private $userRepository;

    public function __construct(UserSourceInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Image $image */
        $image = $options['data'];
        $isEdit = $image && $image->getId();

        $builder
            ->add('transition', WorkflowType::class, [
                'subject' => $image,
                'current_place' => $image->getStatus(),
            ])
            ->add('owner', EntityType::class, [
                'required' => false,
                'label' => 'form.owner.image',
                'class' => User::class,
                'choices' => $this->userRepository->findAll(),
                'choice_value' => static function (?User $entity) {
                    return $entity ? $entity->getId() : '';
                },
                'choice_label' => static function (?User $entity) {
                    return $entity ? $entity->getUsername() : '';
                }
            ])
            ->add('image', \Symfony\Component\Form\Extension\Core\Type\FileType::class, [
                'required' => !$isEdit,
                'mapped' => false,
                'label' => 'form.image',
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\Image([
                        'maxSize' => '5M'
                    ])
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.submit'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}
