<?php

namespace Dolphin\CustomForms\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Status implements ArrayInterface
{
    public function toOptionArray()
    {
        $result = [];
        foreach ($this->getOptions() as $value => $label) {
            $result[] = [
                'value' => $value,
                'label' => $label,
            ];
        }

        return $result;
    }

    public function getOptions()
    {
        return [
            'active' => __('Active'),
            'inactive' => __('Inactive'),
            'pending' => __('Pending'),
        ];
    }
}
