<?php

namespace Dolphin\CustomForms\Model;

use Magento\Framework\Model\AbstractModel;

class DolphinTable extends AbstractModel
{
    const CACHE_TAG = 'dolphin_customforms';

    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init('Dolphin\CustomForms\Model\ResourceModel\DolphinTable');
    }
}
