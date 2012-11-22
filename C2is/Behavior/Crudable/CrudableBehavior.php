<?php

require_once dirname(__FILE__) . '/CrudableBehaviorUtils.php';
require_once dirname(__FILE__) . '/CrudableBehaviorObjectBuilderModifier.php';
require_once dirname(__FILE__) . '/CrudableBehaviorBaseFormTypeBuilder.php';
require_once dirname(__FILE__) . '/CrudableBehaviorBaseListingBuilder.php';
require_once dirname(__FILE__) . '/CrudableBehaviorFormTypeBuilder.php';
require_once dirname(__FILE__) . '/CrudableBehaviorListingBuilder.php';

use \CrudableBehaviorUtils as Utils;

use Symfony\Component\Filesystem\Filesystem;

class CrudableBehavior extends Behavior
{
    // default parameters value
    protected $parameters = array(
        'route_mount' => '/',
        'route_path'  => null,
        'controller'  => "\\C2is\\Provider\\CrudController",
        'model'       => null,
        'form'        => null,
        'type_file'   => null,
    );

    // additional builders
    protected $additionalBuilders = array(
        'CrudableBehaviorBaseFormTypeBuilder',
        'CrudableBehaviorBaseListingBuilder',
        'CrudableBehaviorFormTypeBuilder',
        'CrudableBehaviorListingBuilder',
    );

    protected function getCrudableParameter($name)
    {
        return $this->getTable()->getBehavior('crudable')->getParameter($name);
    }

    public function hasTypeFile()
    {
        return $this->getParameter('type_file') != null;
    }


    public function modifyTable()
    {
        if ($this->getTable()->containsColumn('enabled'))
        {
            throw new Exception(sprintf("The enabled column is automatically added by Crudable Behavior. Please, remove the enabled column on the %s table.", $this->getTable()->getName()), 1);
        }

        $this->getTable()->addColumn(array(
            'name'    => 'enabled',
            'type'    => 'BOOLEAN',
            'default' => true,
        ));
    }

    public function getObjectBuilderModifier()
    {
        if (is_null($this->objectBuilderModifier)) {
            $this->objectBuilderModifier = new CrudableBehaviorObjectBuilderModifier($this);
        }

        return $this->objectBuilderModifier;
    }
}
