<?php

require_once dirname(__FILE__) . '/CrudableBehaviorUtils.php';

use \CrudableBehaviorUtils as Utils;

class CrudableBehaviorBaseFormTypeBuilder extends OMBuilder
{
    public $overwrite = true;

    public function getBehavior()
    {
        return $this->getTable()->getBehavior('crudable');
    }

    public function getPackage()
    {
        return sprintf("%s.Base", str_replace('Model', 'Form.Type', parent::getPackage()));
    }

    public function getUnprefixedClassname()
    {
        return sprintf("Base%sType", $this->getObjectClassname());
    }

    public function getNamespace()
    {
        return str_replace("Model", "Form\\Type\\Base", parent::getNamespace());
    }

    protected function getTypeFileColumns()
    {
        $typeFileString = str_replace(' ', '', $this->getBehavior()->getParameter('type_file'));

        return explode(',', $typeFileString);
    }

    protected function addClassOpen(&$script)
    {
        $classname = $this->getClassname();

        $script .= "use Symfony\\Component\\Form\\AbstractType;
use Symfony\\Component\\Form\\FormBuilderInterface;
use Symfony\\Component\\OptionsResolver\\Options;
use Symfony\\Component\\OptionsResolver\\OptionsResolverInterface;
use Symfony\\Component\\Validator\\Constraints as Assert;

/**
 * @authors Morgan Brunot <brunot.morgan@gmail.com>
 *          Denis Roussel <denis.roussel@gmail.com>
 *
 * @package c2is.behavior.crudable
 */
class {$classname} extends AbstractType
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

        $this->addSetDefaultOptions($script);
        $this->addGetName($script);
    }

    protected function addBuildForm(&$script)
    {
        $script .= "
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface \$builder, array \$options)
    {";

        foreach ($this->generateBuilders() as $builder) {
            $name = $builder['name'];
            $type = $builder['type'];
            $options = Utils::formatArrayToString($builder['options']);

            $script .= "
        \$builder->add('{$name}', '{$type}'{$options});\n";
        }

        $script .= "
    }
";
    }

    protected function getColumnType(Column $column)
    {
        switch ($column->getType()) {
            case PropelTypes::VARCHAR:
                return 'text';
                break;

            case PropelTypes::LONGVARCHAR:
                return 'textarea';
                break;

            case PropelTypes::INTEGER:
                return 'integer';
                break;

            case PropelTypes::BOOLEAN:
                return 'checkbox';
                break;

            case PropelTypes::FLOAT:
                return 'text';
                break;

            case PropelTypes::DATE:
                return 'date';
                break;

            case PropelTypes::TIMESTAMP:
                return 'datetime';
                break;

            case PropelTypes::LONGVARBINARY:
                return 'file';
                break;

            case PropelTypes::ENUM:
                return 'choice';
                break;

            default:
                return $column->getType();
                break;
        }
    }

    protected function addConstraints(Column $column)
    {
        $constraints = array();
        if ($column->getAttribute('required', false)) {
            $constraints[] = 'new Assert\NotBlank()';
        }

        return $constraints;
    }

    protected function generateBuilders()
    {
        $builders = array();

        foreach ($this->getTable()->getColumns() as $column) {
            if ($column->isPrimaryKey()) {
                $builders[] = array(
                    'name'    => $column->getName(),
                    'type'    => 'hidden',
                    'options' => array(
                        'label'    => sprintf("%s.%s", $this->getTable()->getName(), $column->getName()),
                        'required' => false,
                    )
                );
            }
            else if ($column->isForeignKey()) {
                foreach ($column->getForeignKeys() as $foreignColumn) {
                    $name = sprintf("%s_related_by_%s",
                        $foreignColumn->getForeignTable()->getName(),
                        $column->getName()
                    );

                    $builders[] = array(
                        'name'    => $name,
                        'type'    => 'model',
                        'options' => array(
                            'class'       => sprintf('\\%s\\%s', $foreignColumn->getForeignTable()->getNamespace(), $foreignColumn->getForeignTable()->getPhpName()),
                            'label'       => sprintf("%s.%s", $this->getTable()->getName(), $column->getName()),
                            'required'    => false,
                            'constraints' => $this->addConstraints($column),
                        )
                    );
                }
            }
            else {
                $addDeletedColumn = false;

                if (in_array($column->getName(), $this->getTypeFileColumns())) {
                    $column->setType(PropelTypes::LONGVARBINARY);
                    $addDeletedColumn = true;
                }

                $builder = array(
                    'name'    => $column->getName(),
                    'type'    => $this->getColumnType($column),
                    'options' => array(
                        'constraints' => $this->addConstraints($column),
                        'label'       => sprintf("%s.%s", $this->getTable()->getName(), $column->getName()),
                        'required'    => false,
                    )
                );

                if ($column->getType() == PropelTypes::ENUM) {
                    $builder['options'] = array_merge(
                        $builder['options'],
                        array('choices' => array_combine($column->getValueSet(), $column->getValueSet()))
                    );
                }

                $builders[] = $builder;

                if ($addDeletedColumn) {
                    $deletedColumn = clone $column;
                    $deletedColumn->setType(PropelTypes::BOOLEAN);
                    $deletedColumn->setName(sprintf("%s_deleted", $column->getName()));

                    $builders[] = array(
                        'name'    => $deletedColumn->getName(),
                        'type'    => $this->getColumnType($deletedColumn),
                        'options' => array(
                            'label'         => sprintf("%s.%s", $this->getTable()->getName(), $deletedColumn->getName()),
                            'required'      => false,
                            'property_path' => false,
                        )
                    );
                }
            }
        }

        // Manage i18n behavior
        if ($this->getTable()->hasBehavior('i18n'))
        {
            $i18nColumns = array();
            foreach ($this->getTable()->getBehavior('i18n')->getI18nColumns() as $i18nColumn)
            {
                $i18nColumns[$i18nColumn->getName()] = array(
                    'required'      => false,
                    'label'         => sprintf('%s.%s', $this->getTable()->getName(), $i18nColumn->getName()),
                    'type'          => $this->getColumnType($i18nColumn),
                    'constraints'   => $this->addConstraints($i18nColumn),
                );
            }

            $languages = explode(';', $this->getTable()->getGeneratorConfig()->getBuildProperties()['behaviorCrudableLanguages']);

            $options = array(
                'i18n_class' => sprintf('%s\\%sI18n', $this->getTable()->getNamespace(), $this->getTable()->getPhpName()),
                'languages'  => $languages,
                'label'      => sprintf("%s.translations", $tableName = $this->getTable()->getName()),
                'columns'    => $i18nColumns,
            );

            $builders[] = array(
                'name'    => sprintf('%sI18ns', $this->getTable()->getName()),
                'type'    => 'translation_collection',
                'options' => $options,
            );
        }

        return $builders;
    }

    protected function addSetDefaultOptions(&$script)
    {
        $modelClassname = Utils::getModelClassname(
            $this->getTable()->getNamespace(),
            $this->getTable()->getName()
        );

        $script .= "
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface \$resolver)
    {
        \$resolver->setDefaults(array(
            'data_class' => '{$modelClassname}',
        ));
    }
";
    }

    protected function addGetName(&$script)
    {
        $tableName = $this->getTable()->getName();

        $script .= "
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return '{$tableName}';
    }
";
    }
}
