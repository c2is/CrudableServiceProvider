<?php

namespace C2is\Lib\Listing\Filler;

use C2is\Lib\Listing\Column\AbstractColumn;

interface FillerInterface
{
    public function setData(\ArrayObject $data);

    public function getData();

    public function extractData($lineIndex, AbstractColumn $column);
}
