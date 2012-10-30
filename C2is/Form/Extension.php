<?php

namespace C2is\Form;

use Symfony\Component\Form\AbstractExtension;

class Extension extends AbstractExtension
{
    protected function loadTypes()
    {
        return array(
            new \C2is\Form\Type\ModelType(),
            new \C2is\Form\Type\TranslationType(),
            new \C2is\Form\Type\TranslationCollectionType(),
        );
    }
}
