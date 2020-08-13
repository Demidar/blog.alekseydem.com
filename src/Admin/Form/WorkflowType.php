<?php

namespace App\Admin\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Workflow\Exception\TransitionException;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Transition;
use Symfony\Contracts\Translation\TranslatorInterface;

class WorkflowType extends AbstractType
{
    private $workflowRegistry;
    private $translator;

    public function __construct(
        Registry $workflowRegistry,
        TranslatorInterface $translator
    ) {
        $this->workflowRegistry = $workflowRegistry;
        $this->translator = $translator;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'subject' => null,
            'current_place' => null,
            'workflow_name' => null,
            'mapped' => false,
            'translation_domain' => false,
            'choice_translation_domain' => 'workflow',
            'multiple' => false,
            'expanded' => true,
            'required' => false,
            'choice_value' => static function ($transition) {
                return $transition instanceof Transition ? $transition->getName() : null;
            },
            'choice_label' => static function ($transition) {
                return $transition instanceof Transition ? $transition->getName() : null;
            },
        ]);

        $resolver->setNormalizer('choices', function(Options $options) {
            return $this->workflowRegistry->get($options['subject'], $options['workflow_name'])->getEnabledTransitions($options['subject']);
        });

        $resolver->setNormalizer('label', function(Options $options) {
            $currentPlaceLabel = $this->translator->trans('place.'.$options['current_place'], [], 'workflow');
            return $this->translator->trans(
                'form.status',
                ['current' => $currentPlaceLabel]
            );
        });

        $resolver->setAllowedTypes('subject', ['object']);
        $resolver->setAllowedTypes('current_place', ['string', 'null']);
        $resolver->setAllowedTypes('workflow_name', ['string', 'null']);
    }

    /**
     * TODO: Workflow name should be passed. Now it's guessing.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $parentForm = $form->getParent();
            if (!$parentForm) {
                throw new \LogicException('This type should be child.');
            }

            $entity = $parentForm->getData();
            if (!(is_object($entity))) {
                throw new \LogicException('The parent data should be object, or this type should be mapped to an entity.');
            }

            $data = $event->getData();
            $transition = reset($data);

            if ($transition) {
                $workflow = $this->workflowRegistry->get($entity);
                $workflow->apply($entity, $transition);
            }
        });
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
