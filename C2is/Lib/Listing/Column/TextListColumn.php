<?php

namespace C2is\Lib\Listing\Column;

use C2is\Lib\Listing\CellData;

class TextListColumn extends AbstractColumn
{
    public function verifyData(CellData $data)
    {
        return isset($data->getOptions()['list_items']) && is_array($data->getOptions()['list_items']);
    }

    public function renderData(CellData $data)
    {
        return $this->renderer->render('Listing/Column/TextListColumn.twig', array(
            'items'  => $data->getOptions()['list_items']
        ));
    }

    public function getType()
    {
        return 'text_list';
    }
}
