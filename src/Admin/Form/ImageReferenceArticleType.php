<?php

namespace App\Admin\Form;

use App\Entity\ImageReferenceArticle;
use App\Form\ImageSelectorType;
use App\Repository\ImageRepository;
use App\Repository\Interfaces\ImageQueryingInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageReferenceArticleType extends AbstractType
{
    private $imageRepository;

    public function __construct(ImageQueryingInterface $imageRepository)
    {
        $this->imageRepository = $imageRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ImageReferenceArticle $reference */
        $reference = $options['data'];

        $builder
            ->add('status', WorkflowType::class, [
                'subject' => $reference,
                'current_place' => $reference->getStatus()
            ])
            ->add('image', ImageSelectorType::class, [
                'label' => 'form.image',
                'choices' => $this->imageRepository->findImages(),
                'choice_value' => 'id',
                'choice_label' => 'original_name',
            ])
            ->add('article', HeavyArticleSelectorType::class, [
                'data' => $reference->getArticle()
//                'label' => 'article.image',
//                'class' => Article::class,
//                'choice_label' => 'title'
            ])
            ->add('position')
            ->add('submit', SubmitType::class, [
                'label' => 'form.submit'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ImageReferenceArticle::class,
        ]);
    }
}
