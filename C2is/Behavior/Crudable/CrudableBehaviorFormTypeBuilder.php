<?php

require_once dirname(__FILE__) . '/CrudableBehaviorUtils.php';

use \CrudableBehaviorUtils as Utils;

use Symfony\Component\Filesystem\Filesystem;

class CrudableBehaviorFormTypeBuilder extends OMBuilder
{
    public $overwrite = false;

    public function getPackage()
    {
        return str_replace('Model', 'Form.Type', parent::getPackage());
    }

    public function getUnprefixedClassname()
    {
        return sprintf("%sType", $this->getObjectClassname());
    }

    public function getNamespace()
    {
        return str_replace("Model", "Form\\Type", parent::getNamespace());
    }

    protected function addClassOpen(&$script)
    {
        $classname = $this->getClassname();
        $namespace = $this->getNamespace();

        $script .= "use Symfony\\Component\\Form\\FormBuilderInterface;
use Symfony\\Component\\Validator\\Constraints as Assert;
use Symfony\\Component\\Form\FormInterface;
use Symfony\\Component\\Form\FormView;

use {$namespace}\\Base\\Base{$classname};

/**
 * @authors Morgan Brunot <brunot.morgan@gmail.com>
 *          Denis Roussel <denis.roussel@gmail.com>
 *
 * @package c2is.behavior.crudable
 */
class {$classname} extends Base{$classname}
{";
    }

    protected function addClassClose(&$script)
    {
        $classname = $this->getClassname();

        $script .= "
} // {$classname}
";
    }

    protected function addClassBody(&$script)
    {
        $this->addBuildForm($script);
    }

    protected function addBuildForm(&$script)
    {
        $script .= "
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface \$builder, array \$options)
    {
        parent::buildForm(\$builder, \$options);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView \$view, FormInterface \$form, array \$options)
    {
    }
";
    }
}
