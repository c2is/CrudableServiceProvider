<?php

namespace C2is\Lib\Listing;

class CellData
{
    protected $text;
    protected $options = array();

    public function __construct()
    {
        $this->text = '';
    }

    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }
}
