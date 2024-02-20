<?php

namespace Dolphin\CustomForms\Model;

use Magento\Framework\Model\AbstractModel;

class FormData extends AbstractModel
{
    const CACHE_TAG = 'dolphin_custom_form_data';

    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init('Dolphin\CustomForms\Model\ResourceModel\FormData');
    }
}
