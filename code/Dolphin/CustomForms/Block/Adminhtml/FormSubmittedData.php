<?php

namespace Dolphin\CustomForms\Block\Adminhtml;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Dolphin\CustomForms\Model\ResourceModel\FormData\CollectionFactory;

class FormSubmittedData extends Template
{
    protected $formDataFactory;

    public function __construct(
        Context $context,
        CollectionFactory $formDataFactory
    )
    {
        $this->formDataFactory = $formDataFactory;
        parent::__construct($context);
    }

    public function getId()
    {
        return $id = $this->getRequest()->getParam('id');
    }

    public function getCollection()
    {
        $currentId = $this->getId();
        $collection = $this->formDataFactory->create();
        $collection->addFieldToFilter('id', $currentId);

        return $collection;
    }
}
