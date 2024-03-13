<?php

namespace Dolphin\CustomForms\Block\Adminhtml;

use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Dolphin\CustomForms\Model\ResourceModel\FormData\CollectionFactory;

class FormSubmittedData extends Template
{
    protected $formDataFactory;
    protected $filesystem;
    protected $mediaDirectory;

    public function __construct(
        Context $context,
        CollectionFactory $formDataFactory,
        Filesystem $filesystem
    )
    {
        $this->formDataFactory = $formDataFactory;
        $this->mediaDirectory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
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

    public function getFilePath()
    {
        $formData = $this->getCollection()->getFirstItem();
        $fileName = $formData->getData('uploaded_file');

        if (!empty($fileName)) {
            $filePath = 'custom_forms_images/uploads/' . $fileName; // Adjust the path as needed
            return $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $filePath;
        }

        return false;
    }
}
