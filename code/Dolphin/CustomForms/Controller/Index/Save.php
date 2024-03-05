<?php

namespace Dolphin\CustomForms\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultFactory;
use Dolphin\CustomForms\Model\FormDataFactory;
use Zend\Log\Filter\Timestamp;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;

class Save extends Action
{
    const XML_PATH_EMAIL_RECIPIENT_NAME = 'trans_email/ident_general/name';
    const XML_PATH_EMAIL_RECIPIENT_EMAIL = 'trans_email/ident_general/email';

    protected $inlineTranslation;
    protected $transportBuilder;
    protected $scopeConfig;
    protected $logger;
    protected $storeManager;
    protected $formDataFactory;
    private JsonFactory $resultJsonFactory;

    public function __construct(
        Context $context,
        FormDataFactory $formDataFactory,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger,
        StoreManagerInterface $storeManager,
        JsonFactory $resultJsonFactory,
        array $data = []

    ) {
        parent::__construct($context);
        $this->formDataFactory = $formDataFactory;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPostValue();

            if ($formData) {
                try {
                    $formId = $formData['form_id'];
                    $widgetName = $formData['form_name'];
                    $formDataModel = $this->formDataFactory->create();
                    $formDataModel->setData([
                        'form_id' => $formId,
                        'form_name' => $widgetName,
                        'form_data' => json_encode($formData),
                    ]);
                    $formDataModel->save();
                    $this->sendEmail($formId, $widgetName, $formData);
                    $result->setData(['success' => true, 'message' => __('Form data saved successfully.')]);
                    return $result;
                } catch (\Exception $e) {
                    $result->setData(['success' => false, 'message' => __($e->getMessage())]);
                    return $result;
                }
            }
        }

        // Redirect to the referrer or a default page if the referrer is not available
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl() ?: $this->_redirect->getHomePageUrl());
        return $resultRedirect;
    }

    protected function sendEmail($formId, $widgetName, $formData)
    {
        $recipientName = $this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT_NAME);
        $recipientEmail = $this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT_EMAIL);
        $store = $this->storeManager->getStore();

        $transport = $this->transportBuilder
            ->setTemplateIdentifier('custom_email_template')
            ->setTemplateOptions(['area' => 'frontend', 'store' => $store->getId()])
            ->setTemplateVars(['form_id' => $formId, 'widget_name' => 'product_inquiry_widget', 'form_data' => 'Product Inquiry Data'])
            ->setFrom(['email' => $recipientEmail, 'name' => $recipientName])
            ->addTo($recipientEmail, $recipientName)
            ->getTransport();

        $transport->sendMessage();
    }
}
