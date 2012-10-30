<?php

require_once dirname(__FILE__) . '/CrudableBehaviorUtils.php';

use \CrudableBehaviorUtils as Utils;

class CrudableBehaviorObjectBuilderModifier
{
    protected $behavior, $table, $builder;

    public function __construct($behavior)
    {
        $this->behavior = $behavior;
        $this->table = $behavior->getTable();
    }

    protected function getParameter($key)
    {
        return $this->behavior->getParameter($key);
    }

    protected function getCrudableParameters()
    {
        return $this->behavior->getParameters();
    }

    protected function getTypeFileColumns()
    {
        $typeFileString = str_replace(' ', '', $this->behavior->getParameter('type_file'));

        return explode(',', $typeFileString);
    }

    public function objectMethods($builder)
    {
        $this->builder = $builder;

        // generate confif files
        $this->generateControllersConfig();
        $this->generateLanguagesConfig();

        $script = $this->addSaveFromCrud($builder);

        if ($this->behavior->hasTypeFile()) {
            $script .= $this->addGetUploadDir($builder);
            $script .= $this->addGetUploadRootDir($builder);

            foreach ($this->getTypeFileColumns() as $column) {
                $script .= $this->addUploadFile($builder, $column);
            }
        }

        return $script;
    }

    public function addSaveFromCrud($builder)
    {
        $columns = array();

        if ($this->behavior->hasTypeFile()) {
            foreach ($this->getTypeFileColumns() as $column) {
                $columns[] = array(
                    'filecolumnName'     => Utils::camelize($column),
                    'deletedColumnName'  => sprintf("%s_deleted", $column),
                    'fileColumnPeerName' => sprintf("%sPeer::%s", Utils::camelize($this->behavior->getTable()->getName()), strtoupper($column)),
                );
            }
        }

        return $this->behavior->renderTemplate('objectSaveFromCrud', array(
            'columns' => $columns,
        ));
    }

    public function addGetUploadDir($builder)
    {
        return $this->behavior->renderTemplate('objectGetUploadDir', array(
            'dir' => sprintf("%ss", $this->behavior->getTable()->getName()),
        ));
    }

    public function addGetUploadRootDir($builder)
    {
        $absoluteModelDir = realpath(sprintf('%s/%s/%s',
            $this->builder->getTable()->getGeneratorConfig()->getBuildProperties()['phpDir'],
            str_replace('\\', '/', $this->builder->getTable()->getDatabase()->getNamespace()),
            $this->builder->getTable()->getGeneratorConfig()->getBuildProperties()['namespaceOm']
        ));

        $absoluteWebDir = realpath($this->builder->getTable()->getGeneratorConfig()->getBuildProperties()['behaviorCrudableWebDir']);

        $relativeWebDir = implode('/', array_diff_assoc(explode('/', $absoluteWebDir), explode('/', $absoluteModelDir)));
        $subLevel       = str_repeat('/..', count(array_diff_assoc(explode('/', $absoluteModelDir), explode('/', $absoluteWebDir))));

        return $this->behavior->renderTemplate('objectGetUploadRootDir', array(
            'dir' => sprintf("%s/%s/", $subLevel, $relativeWebDir),
        ));
    }

    public function addUploadFile($builder, $column)
    {
        return $this->behavior->renderTemplate('objectUploadFile', array(
            'column'         => $column,
            'columnCamelize' => Utils::camelize($column),
        ));
    }

    protected function generateControllersConfig()
    {
        // mandatory parameters
        $mandatoryParameters = array(
            'route_path'
        );

        // crudable parameters
        $crudableParameters = $this->getCrudableParameters();

        // validate that the parameter are set
        foreach ($mandatoryParameters as $mandatoryParameter) {
            if (null === $crudableParameters[$mandatoryParameter]) {
                return;
            }
        }

        // validate the existence of the crud configuration file
        $phpConfDir   = $this->behavior->getTable()->getGeneratorConfig()->getBuildProperties()['behaviorCrudablePhpconfDir'];
        $crudFilename = sprintf("%s/controllers-conf.php", rtrim($phpConfDir, '/'));

        $modelClassname = Utils::getModelClassname(
            $this->behavior->getTable()->getNamespace(),
            $this->behavior->getTable()->getName()
        );

        $formClassname = Utils::getFormClassname(
            $this->behavior->getTable()->getNamespace(),
            $this->behavior->getTable()->getName()
        );

        if (!file_exists($crudFilename)) {
            $fs = new Filesystem();
            $fs->touch($crudFilename);
            $controllersConf = array();
        }
        else {
            $controllersConf = require $crudFilename;
        }

        if (!is_array($controllersConf)) {
            $fs = new Filesystem();
            $fs->remove($crudFilename);

            throw new Exception("Error Processing CrudableBehavior::generateControllersConfig()", 1);
        }

        $controllersConf[$this->behavior->getTable()->getName()] = array(
            'route_mount' => $crudableParameters['route_mount'],
            'route_path'  => $crudableParameters['route_path'] ?: sprintf('/%s', $this->behavior->getTable()->getName()),
            'controller'  => $crudableParameters['controller'],
            'model'       => $crudableParameters['crud_model'] ?: $modelClassname,
            'form'        => $crudableParameters['crud_form'] ?: $formClassname,
        );

        // write the controllers configuration file
        file_put_contents(
            $crudFilename,
            sprintf("<?php\nreturn %s;\n", var_export($controllersConf, true))
        );
    }

    protected function generateLanguagesConfig()
    {
        // validate the existence of the languages configuration file
        $phpConfDir        = $this->behavior->getTable()->getGeneratorConfig()->getBuildProperties()['behaviorCrudablePhpconfDir'];
        $languagesFilename = sprintf("%s/languages-conf.php", rtrim($phpConfDir, '/'));

        if (!file_exists($languagesFilename)) {
            $fs = new Filesystem();
            $fs->touch($languagesFilename);
        }

        $languagesString = str_replace(' ', '', $this->behavior->getTable()->getGeneratorConfig()->getBuildProperties()['behaviorCrudableLanguages']);
        $languagesConf   = explode(';', $languagesString);

        $languagesConf = array_map(function($value) {
            return array(
                'locale'   => $value,
                'filename' => sprintf("trans-%s.yml", $value),
            );
        }, $languagesConf);

        // write the controllers configuration file
        file_put_contents(
            $languagesFilename,
            sprintf("<?php\nreturn %s;\n", var_export($languagesConf, true))
        );
    }
}
