<?php

namespace Dolphin\CustomForms\Model\ResourceModel\FormData;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Dolphin\CustomForms\Model\FormData', 'Dolphin\CustomForms\Model\ResourceModel\FormData');
    }
}
