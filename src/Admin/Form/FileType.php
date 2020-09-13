<?php

namespace App\Admin\Form;

use App\Entity\File;
use App\Entity\User;
use App\Repository\Interfaces\UserSourceInterface;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FileType extends AbstractType
{
    private $userRepository;

    public function __construct(UserSourceInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var File $file */
        $file = $options['data'];
        $isEdit = $file && $file->getId();

        $builder
            ->add('transition', WorkflowType::class, [
                'subject' => $file,
                'current_place' => $file->getStatus(),
            ])
            ->add('owner', EntityType::class, [
                'required' => false,
                'label' => 'form.owner.file',
                'class' => User::class,
                'choices' => $this->userRepository->findAll(),
                'choice_value' => static function (?User $entity) {
                    return $entity ? $entity->getId() : '';
                },
                'choice_label' => static function (?User $entity) {
                    return $entity ? $entity->getUsername() : '';
                }
            ])
            ->add('file', \Symfony\Component\Form\Extension\Core\Type\FileType::class, [
                'mapped' => false,
                'required' => !$isEdit,
                'label' => 'form.file',
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\File([
                        'maxSize' => '10M'
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
            'data_class' => File::class,
        ]);
    }
}
