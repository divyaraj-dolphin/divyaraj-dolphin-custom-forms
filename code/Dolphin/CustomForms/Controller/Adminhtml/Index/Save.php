<?php

namespace Dolphin\CustomForms\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Dolphin\CustomForms\Model\DolphinTableFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Controller\ResultFactory;

class Save extends Action
{
    protected $customFactory;
    protected $logger;

    public function __construct(
        Action\Context $context,
        DolphinTableFactory $customFactory,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->customFactory = $customFactory;
        $this->logger = $logger;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        try {
            // Check if there is an existing record to update
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

            // Update fields
            $model->addData([
                "name" => $data['name'],
                "email" => $data['email'],
                "phoneno" => $data['phoneno'],
                "custom_formbuilder_data" => $data['custom_formbuilder_data'],
            ]);
//            $this->logger->info("Form Builder Hidden Data: " . $data['formbuilder_hidden_data']);
            // Save the record
            $model->save();

            $this->messageManager->addSuccess(__($message));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
            $this->logger->info("Data wasn't saved");
        }

        // Redirect to the listing page
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/index');
        return $resultRedirect;
    }
}
