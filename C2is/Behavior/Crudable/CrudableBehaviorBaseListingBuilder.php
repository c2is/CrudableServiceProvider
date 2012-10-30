<?php

require_once dirname(__FILE__) . '/CrudableBehaviorUtils.php';

use \CrudableBehaviorUtils as Utils;

class CrudableBehaviorBaseListingBuilder extends OMBuilder
{
    public $overwrite = true;

    public function getPackage()
    {
        return str_replace('Model', 'Listing', parent::getPackage()) . ".Base";
    }

    public function getUnprefixedClassname()
    {
        return 'Base' . $this->getStubObjectBuilder()->getUnprefixedClassname() . 'Listing';
    }

    public function getNamespace($namespace = null)
    {
        return sprintf('%s\Base', $this->getChildrenNamespace($namespace));
    }

    public function getChildrenNamespace($namespace = null)
    {
        if ($namespace === null) {
            $namespace = $this->getTable()->getNamespace();
        }

        return str_replace('Model', 'Listing', $namespace);
    }

    protected function addClassOpen(&$script)
    {
        $table     = $this->getTable();
        $tableName = $table->getName();

        $classname = $this->getClassname();

        $script .= "use C2is\\Lib\\Listing\\Listing;
use C2is\\Lib\\Listing\\Column;

/**
 * @authors Morgan Brunot <brunot.morgan@gmail.com>
 *          Denis Roussel <denis.roussel@gmail.com>
 *
 * @package c2is.behavior.crudable
 */
class {$classname} extends Listing
{";
    }

    protected function addClassBody(&$script)
    {
        $this->addConfigure($script);
        $this->addGetName($script);
    }

    protected function addGetName(&$script)
    {
        $name = $this->getStubObjectBuilder()->getUnprefixedClassname();

        $script .= "
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return '{$name}';
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

    protected function generateColumns(&$script)
    {
        $columns = array();

        foreach ($this->getTable()->getColumns() as $column) {
            if ($column->isPrimaryKey()) {
                $columns[] = array(
                    'name' => $column->getName(),
                    'type' => 'Text'
                );
            }
            else if ($column->getType() == PropelTypes::VARCHAR) {
                $columns[] = array(
                    'name' => $column->getName(),
                    'type' => 'Text'
                );
            }
            else if ($column->getType() == PropelTypes::TIMESTAMP || $column->getType() == PropelTypes::DATE) {
                if (!in_array($column->getName(), array('created_at', 'updated_at'))) {
                    $columns[] = array(
                        'name' => $column->getName(),
                        'type' => 'Date'
                    );
                }
            }
        }

        return $columns;
    }

    protected function addConfigure(&$script)
    {
        $script .= "
    /**
     * {@inheritdoc}
     */
    public function configure()
    {";

        foreach ($this->generateColumns() as $columns) {
            $name = $columns['name'];
            $type = $columns['type'];

            $script .= "
        \$this->addColumn(new Column\\{$type}Column('{$name}'));\n";
        }

        $script .= "
    }
";
    }
}
