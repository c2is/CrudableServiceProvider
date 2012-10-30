<?php

namespace C2is\Lib\Listing\Filler;

use C2is\Lib\Listing\Column\AbstractColumn;
use C2is\Lib\Listing\CellData;

abstract class AbstractDatabaseFiller extends AbstractFiller
{
    public function extractData($lineIndex, AbstractColumn $column)
    {
        $cell   = new CellData();

        $data   = $this->data[$lineIndex];
        $name   = $column->getName();
        $info   = array();

        if ($this->isMultipleFK($name, $data)) {
            $textFieldName = (isset($column->getOptions()['text_field_name'])) ? $column->getOptions()['text_field_name'] : null;
            $info = $this->getMulitpleFKInfo($name, $data, $textFieldName);
        }
        elseif ($this->isSingleFK($name, $data)) {
            $textFieldName = (isset($column->getOptions()['text_field_name'])) ? $column->getOptions()['text_field_name'] : null;
            $info[] = $this->getSingleFKInfo($name, $data, $textFieldName);
        }
        else {
            $info[] = $this->getFieldInfo($name, $data);
        }

        switch($column->getType()) {
            case 'text':
                $cell->setText($info[0]['text']);
                break;

            case 'link':
                $cell
                    ->setText($info[0]['text'])
                    ->setOptions(array(
                        'title' => $info[0]['text'],
                        'link' => 'http://link'
                    ))
                ;
                break;

            case 'text_list':
                $cell->setOptions(array(
                    'list_items' => $info
                ));
                break;

            default:
                throw new \Exception(sprintf('Colmun type "%s" is not handled by %s', $column->getType(), __CLASS__));
        }

        return $cell;
    }

    abstract protected function getFieldInfo     ($name, $data);
    abstract protected function isSingleFK       ($name, $data);
    abstract protected function isMultipleFK     ($name, $data);
    abstract protected function getSingleFKInfo  ($name, $data, $textFieldName = null);
    abstract protected function getMulitpleFKInfo($name, $data, $textFieldName = null);
}
