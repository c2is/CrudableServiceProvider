<?php

namespace C2is\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TranslationCollectionType extends CollectionType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $languages = $options['languages'];
        $i18nClass = $options['i18n_class'];

        $options['options']['data_class'] = $i18nClass;
        $options['options']['columns'] = $options['columns'];

        $callable = function(DataEvent $event) use ($builder, $languages, $i18nClass) {
            $form = $event->getForm();
            $data = $event->getData();

            if ($data == null) {
                return;
            }

            //get the class name of the i18nClass
            $temp = explode('\\', $i18nClass);
            $dataClass = end($temp);

            $rootData = $form->getRoot()->getData();

            //add a row for every needed language
            foreach ($languages as $lang) {
                $found = false;

                foreach ($data as $i18n) {
                    if ($i18n->getLocale() == $lang) {
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $newTranslation = new $i18nClass();
                    $newTranslation->setLocale($lang);

                    $addFunction = 'add'.$dataClass;
                    $rootData->$addFunction($newTranslation);
                }
            }
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, $callable);

        parent::buildForm($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'translation_collection';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'languages'    => array(),
            'i18n_class'   => '',
            'columns'      => array(),
            'type'         => 'translation',
            'allow_add'    => false,
            'allow_delete' => false
        ));
    }
}
