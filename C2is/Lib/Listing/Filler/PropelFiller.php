<?php

namespace C2is\Lib\Listing\Filler;

use C2is\Lib\Listing\Column\AbstractColumn;
use C2is\Lib\Listing\CellData;
use C2is\Lib\String;

class PropelFiller extends AbstractDatabaseFiller
{
    protected function getFieldInfo($name, $data)
    {
        $getter = 'get'.String::camelize($name);

        return array(
            'text' => $data->$getter()
        );
    }

    protected function isSingleFK($name, $data)
    {
        $method = 'get'.String::camelize($name).'Id';

        return method_exists($data, $method);
    }

    protected function isMultipleFK($name, $data)
    {
        $plurializer = new \DefaultEnglishPluralizer();

        $method = 'get'.String::camelize($plurializer->getPluralForm($name));

        return method_exists($data, $method);
    }

    protected function getSingleFKInfo($name, $data, $textFieldName = null)
    {
        $method = 'get'.String::camelize($name);
        $object = $data->$method();

        $getTextMethod = '__toString';
        if (!is_null($textFieldName)) {
            $getTextMethod = 'get'.String::camelize($textFieldName);
        }

        $text = ($object) ? $object->$getTextMethod() : '';
        $id   = ($object) ? $object->getId() : '';

        return array(
            'text' => $text,
            'id'   => $id
        );
    }

    protected function getMulitpleFKInfo($name, $data, $textFieldName = null)
    {
        $plurializer = new \DefaultEnglishPluralizer();
        $info        = array();

        $method  = 'get' . String::camelize($plurializer->getPluralForm($name));
        $objects = $data->$method();

        $getTextMethod = '__toString';
        if (!is_null($textFieldName)) {
            $getTextMethod = 'get'.String::camelize($textFieldName);
        }

        var_dump($object);die;

        if (count($objects)) {
            foreach ($objects as $object) {
                $info[] = array(
                    'text' => $object->$getTextMethod(),
                    'id'   => $object->getId()
                );
            }
        }

        return $info;
    }
}
