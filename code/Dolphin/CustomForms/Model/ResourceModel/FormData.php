<?php

namespace Dolphin\CustomForms\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class FormData extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('dolphin_custom_form_data', 'id');
    }
}
