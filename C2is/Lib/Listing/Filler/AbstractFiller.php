<?php

namespace C2is\Lib\Listing\Filler;

use C2is\Lib\Listing\Filler\FillerInterface;

abstract class AbstractFiller implements FillerInterface
{
    protected $data;
    protected $options = array();

    public function __construct(\ArrayObject $data = null)
    {
        if (!is_null($data)) {
            $this->data = $data;
        }
    }

    public function setData(\ArrayObject $data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setOptions(array $options)
    {
        $this->options = $options;
    }
}
