<?php

namespace Dolphin\CustomForms\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Dolphin\CustomForms\Model\FormDataFactory;

class Save extends Action
{
    protected $formdataFactory;

    public function __construct(
        Context $context,
        FormDataFactory $formdataFactory
    ) {
        parent::__construct($context);
        $this->formdataFactory = $formdataFactory;
    }

    public function execute()
    {
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPostValue();

            if ($formData) {
                try {
                    $formId = $formData['form_id'];
                    $widgetName = $formData['form_name'];
                    $formDataModel = $this->formdataFactory->create();
                    $formDataModel->setData([
                        'form_id' => $formId,
                        'form_name' => $widgetName,
                        'form_data' => json_encode($formData),
                    ]);
                    $formDataModel->save();

                    $this->messageManager->addSuccessMessage(__('Form data saved successfully.'));
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(__('An error occurred while saving the form data.'));
                    $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                }
            }
        }

        // Redirect to the referrer or a default page if the referrer is not available
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl() ?: $this->_redirect->getHomePageUrl());
        return $resultRedirect;
    }
}
