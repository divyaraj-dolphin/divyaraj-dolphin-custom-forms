<?php

namespace Dolphin\CustomForms\Model;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Dolphin\CustomForms\Model\ResourceModel\FormData\CollectionFactory;
use Psr\Log\LoggerInterface;

class DataProviderFormSubmission extends AbstractDataProvider
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    protected $loadedData;
    protected $logger;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        LoggerInterface $logger,
        \Magento\Framework\Registry $registry,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->registry = $registry;
        $this->logger = $logger;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }


        
        $items = $this->collection->getItems();

        // Log the collection size
        $collectionSize = $this->collection->getSize();
        $this->logger->info('Collection Size: ' . $collectionSize);

        // Log each item in the collection
        foreach ($items as $item) {
            $this->logger->info('Item ID: ' . $item->getId());
            $this->loadedData[$item->getId()] = $item->getData();
        }

        return $this->loadedData;
    }
}
