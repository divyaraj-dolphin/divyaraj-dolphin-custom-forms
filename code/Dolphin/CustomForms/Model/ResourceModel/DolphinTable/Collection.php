<?php

namespace Dolphin\CustomForms\Model\ResourceModel\DolphinTable;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Dolphin\CustomForms\Model\DolphinTable','Dolphin\CustomForms\Model\ResourceModel\DolphinTable');
    }
}
