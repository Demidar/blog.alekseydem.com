<?php

namespace App\Admin\Form;

use App\Admin\Form\Transformer\ArticleToObjectTransformer;
use App\Entity\Article;
use App\Repository\ArticleQuerying;
use App\Repository\Interfaces\ArticleSourceInterface;
use App\Repository\ModifierParams\ArticleQueryModifierParams;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * More optimized way to get all articles with exact fields.
 */
class HeavyArticleSelectorType extends AbstractType
{
    private $articleToObjectTransformer;
    private $articleRepository;

    public function __construct(
        ArticleToObjectTransformer $articleToObjectTransformer,
        ArticleSourceInterface $articleRepository
    ) {
        $this->articleToObjectTransformer = $articleToObjectTransformer;
        $this->articleRepository = $articleRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->articleToObjectTransformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => 'form.relation.article',
            'invalid_message' => 'The selected article does not exist',
            /* TODO: this may be heavy when there are more than 1000 articles.
             *  Try implement loading through AJAX while filling a field.
             */
            // convert array of arrays to array of object to avoid "choices grouping"
            'choices' => array_map(
                static function ($entry) {return (object) $entry;},
                $this->articleRepository->findArticles(new ArticleQueryModifierParams([
                    'select' => ['id', 'title', 'status'],
                    'orderByField' => 'title',
                ]))
            ),
            'choice_value' => static function (?object $entity) {
                return $entity->id ?? null;
            },
            'choice_translation_domain' => false,
            'choice_label' => static function (?object $entity) {
                return $entity ? $entity->title . ' (' . $entity->status . ')' : '';
            }
        ]);

        $resolver->setNormalizer('data', static function (Options $options, $value) {
            return (object) ['id' => $value->getId()];
        });

        $resolver->setAllowedTypes('data', [Article::class]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
