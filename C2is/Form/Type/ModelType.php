<?php

namespace C2is\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use C2is\Form\Extension\ChoiceList\ModelChoiceList;
use C2is\Form\DataTransformer\CollectionToArrayTransformer;

class ModelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['multiple'])
        {
            $builder->addViewTransformer(new CollectionToArrayTransformer(), true);
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $choiceList = function (Options $options) {
            return new ModelChoiceList(
                $options['class'],
                $options['property'],
                $options['choices'],
                $options['query'],
                $options['group_by']
            );
        };

        $resolver->setDefaults(array(
            'template'          => 'choice',
            'multiple'          => false,
            'expanded'          => false,
            'class'             => null,
            'property'          => null,
            'query'             => null,
            'choices'           => null,
            'choice_list'       => $choiceList,
            'group_by'          => null,
            'by_reference'      => false,
        ));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'model';
    }
}
