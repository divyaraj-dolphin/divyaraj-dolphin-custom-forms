<?php

namespace Dolphin\CustomForms\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Dolphin\CustomForms\Model\DolphinTableFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Controller\ResultFactory;

class Save extends Action
{
    protected $customFactory;
    protected $logger;
    protected $storeManager;

    public function __construct(
        Action\Context $context,
        DolphinTableFactory $customFactory,
        LoggerInterface $logger,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->customFactory = $customFactory;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $this->logger->info(print_r($data, true));
        try {
            if (!empty($data['id'])) {
                // Load the existing record
                $model = $this->customFactory->create()->load($data['id']);
                $data['formbuilder_hidden_data'] = $model->getData('formbuilder_hidden_data');
                $message = 'Data updated successfully.';
            } else {
                // If 'id' is not provided, create a new record
                $model = $this->customFactory->create();
                $message = 'Data inserted successfully.';
            }

            $storeIds = isset($data['store_id']) ? $data['store_id'] : [];
            $storeIdsString = implode(',', $storeIds);

            $storeNames = $this->getStoreNamesByIds($storeIds);

            $model->addData([
                "form_name" => $data['form_name'],
                "thankyou_message" => $data['thankyou_message'],
                "formbuilder_data" => $data['formbuilder_data'],
                "form_status" => $data['form_status'],
                "store_ids" => $storeIdsString
            ]);
            $model->save();
            $data['form_status'] = $model->getData('form_status');

            $this->messageManager->addSuccess(__($message));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
            $this->logger->info("Data wasn't saved");
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/index');
        return $resultRedirect;
    }

    public function getStoreNamesByIds($storeIds)
    {
        $storeNames = [];
        foreach ($storeIds as $storeId) {
            $store = $this->storeManager->getStore($storeId);
            $storeNames[] = $store->getName();
        }
        return $storeNames;
    }
}
