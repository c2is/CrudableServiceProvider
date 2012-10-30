<?php

namespace C2is\Lib\Listing\Column;

use C2is\Lib\Listing\CellData;

class TextColumn extends AbstractColumn
{
    public function verifyData(CellData $data)
    {
        return true;
    }

    public function renderData(CellData $data)
    {
        return $data->getText();
    }

    public function getType()
    {
        return 'text';
    }
}
