<?php

namespace App\Admin\Field;

use App\Admin\Type\WysiwygType;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;

class WysiwygField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, ?string $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            // used in 'index' and 'details' pages
            ->setTemplateName('crud/field/textarea')
            ->setFormType(WysiwygType::class)
        ;
    }
}
