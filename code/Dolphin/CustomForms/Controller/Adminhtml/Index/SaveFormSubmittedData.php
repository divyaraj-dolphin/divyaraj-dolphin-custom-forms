<?php

namespace Dolphin\CustomForms\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Dolphin\CustomForms\Model\FormDataFactory;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Controller\ResultFactory;
use tests\verification\Tests\DataActionsTest;

class SaveFormSubmittedData extends Action
{
    protected $formDataFactory;
    protected $logger;
    protected $resultPageFactory;

    public function __construct(
        Action\Context $context,
        FormDataFactory $formDataFactory,
        LoggerInterface $logger,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->formDataFactory = $formDataFactory;
        $this->logger = $logger;
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPageFactory = $this->resultRedirectFactory->create();
//        $resultPage->getConfig()->getTitle()->prepend('Form Submitted Data Edit');

        $data = $this->getRequest()->getPostValue();

        try {
            $formDataId = $data['id']; // Assuming you have an 'id' field in your form data

            // Load the existing form data
            $formData = $this->formDataFactory->create()->load($formDataId);

            // Update the form_status field
            $formData->setFormStatus($data['form_status']);

            // Save the updated form data
            $formData->save();

            $this->messageManager->addSuccessMessage(__('Form Status has been successfully updated.'));
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }

        return $resultPageFactory->setPath('*/*/formsubmission');
    }
}
