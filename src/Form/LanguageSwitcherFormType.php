<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\Choice;

class LanguageSwitcherFormType extends AbstractType
{
    /**
     * @var string[]
     */
    private $supportedLocales;
    private $request;
    private $urlGenerator;

    public function __construct(string $bound_supportedLocales, RequestStack $requestStack, UrlGeneratorInterface $urlGenerator)
    {
        $this->supportedLocales = $bound_supportedLocales ? explode('|', $bound_supportedLocales) : [];
        $this->request = $requestStack->getCurrentRequest();
        $this->urlGenerator = $urlGenerator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = $this->getLangChoices();

        $builder
            ->setAction($this->urlGenerator->generate('language-switcher'))
            ->setMethod('POST')
            ->add('lang', ChoiceType::class, [
                'translation_domain' => false,
                'label' => false,
                'attr' => ['onChange' => 'this.form.submit()'],
                'choices' => $choices,
                'expanded' => false,
                'multiple' => false,
                'data' => $this->request->getLocale(),
                'constraints' => [
                    new Choice([
                        'choices' => $this->supportedLocales
                    ])
                ]
            ])
        ;
    }

    private function getLangChoices(): array
    {
        $choices = [];
        foreach ($this->supportedLocales as $locale) {
            switch ($locale) {
                case 'ru':
                    $choices['Рус'] = 'ru';
                    break;
                case 'en':
                    $choices['Eng'] = 'en';
                    break;
                default:
                    $choices[$locale] = $locale;
                    break;
            }
        }
        return $choices;
    }
}
