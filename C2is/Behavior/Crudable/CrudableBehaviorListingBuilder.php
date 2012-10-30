<?php

class CrudableBehaviorListingBuilder extends OMBuilder
{
    public $overwrite = false;

    public function getPackage()
    {
        return str_replace('Model', 'Listing', parent::getPackage());
    }

    public function getParentNamespace($namespace = null)
    {
        return sprintf('%s\Base', $this->getNamespace($namespace));
    }

    public function getNamespace($namespace = null)
    {
        if ($namespace === null)
        {
            $namespace = $this->getTable()->getNamespace();
        }

        return str_replace('Model', 'Listing', $namespace);
    }

    public function getUnprefixedClassname()
    {
        return $this->getStubObjectBuilder()->getUnprefixedClassname() . 'Listing';
    }

    protected function addClassOpen(&$script)
    {
        $classname = $this->getClassname();
        $namespace = $this->getNamespace();

        $script .= "use C2is\\Lib\\Listing\\Listing;
use C2is\\Lib\\Listing\\Column;

use {$namespace}\\Base\\Base{$classname};

/**
 * @authors Morgan Brunot <brunot.morgan@gmail.com>
 *          Denis Roussel <denis.roussel@gmail.com>
 *
 * @package c2is.behavior.crudable
 */
class {$classname} extends Base{$classname}
{
";
    }

    protected function addClassBody(&$script)
    {
        $this->addConfigure($script);
    }

    protected function addConfigure(&$script)
    {
        $script .= "
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        parent::configure();
    }
";
    }

    protected function addClassClose(&$script)
    {
        $classname = $this->getClassname();

        $script .= "
} // {$classname}
";
    }
}
