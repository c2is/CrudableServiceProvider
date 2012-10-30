<?php

namespace C2is\Lib\Listing\Column;

use C2is\Lib\Listing\CellData;

class LinkColumn extends AbstractColumn
{
    public function verifyData(CellData $data)
    {
        if (strlen($data->getText())) {
            return isset($data->getOptions()['link']) && strlen($data->getOptions()['link']);
        }
    }

    public function renderData(CellData $data)
    {
        return $this->renderer->render('Listing/Column/LinkColumn.twig', array(
            'text'  => $data->getText(),
            'link'  => $data->getOptions()['link'],
            'title' => isset($data->getOptions()['title']) ? $data->getOptions()['title'] : $data->getText()
        ));
    }

    public function getType()
    {
        return 'link';
    }
}
