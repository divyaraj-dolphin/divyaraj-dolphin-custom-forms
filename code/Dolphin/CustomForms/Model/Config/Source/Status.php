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
            'new' => __('New'),
            'viewed' => __('Viewed'),
            'in_progress' => __('In Processing'),
            'on_hold' => __('On Hold'),
            'awaiting_user' => __('Awaiting User'),
            'answered' => __('Answered'),
            'approved' => __('Approved'),
            'rejected' => __('Rejected'),
            'closed' => __('Closed'),
            'complete' => __('Complete'),
        ];
    }
}
