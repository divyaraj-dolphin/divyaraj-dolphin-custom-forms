<?php

namespace Dolphin\CustomForms\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class DolphinTable extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('dolphin_custom_form', 'id');
    }
}
