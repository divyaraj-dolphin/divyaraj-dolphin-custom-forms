<?php

namespace Dolphin\CustomForms\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultFactory;
use Dolphin\CustomForms\Model\FormDataFactory;
use Magento\Framework\FileSystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Zend\Log\Filter\Timestamp;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Dolphin\CustomForms\Helper\Data as DolphinHelper;
use Psr\Log\LoggerInterface;

class Save extends Action
{

    protected $inlineTranslation;
    protected $transportBuilder;
    protected $logger;
    protected $storeManager;
    protected $formDataFactory;
    protected $dolphinHelper;
    protected $resultJsonFactory;
    protected $uploaderFactory;
    protected $fileSystem;

    public function __construct(
        Context $context,
        FormDataFactory $formDataFactory,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        LoggerInterface $logger,
        StoreManagerInterface $storeManager,
        JsonFactory $resultJsonFactory,
        DolphinHelper $dolphinHelper,
        UploaderFactory $uploaderFactory,
        FileSystem $fileSystem,
        array $data = []

    ) {
        parent::__construct($context);
        $this->formDataFactory = $formDataFactory;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->dolphinHelper = $dolphinHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->fileSystem = $fileSystem;
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        if ($this->getRequest()->isPost()) {
            try {
                $formData = $this->getRequest()->getPostValue();
                $fileData = $this->getRequest()->getFiles();
//                echo "<pre>";
//                print_r($fileData);
//                print_r($_FILES);
//                exit;
                if ($formData) {
                    $mediaDirectory = $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA);
                    foreach ($fileData as $fieldName => $file) {
                        if (!empty($file['name'])) {
                            $uploader = $this->uploaderFactory->create(['fileId' => $fieldName]);
                            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                            $uploader->setAllowRenameFiles(true);
                            $uploader->setFilesDispersion(false);
                            $uploader->setAllowCreateFolders(true);

                            $resultImage = $uploader->save($mediaDirectory->getAbsolutePath('custom_forms_images/uploads'));

                            $formData = array_merge($formData, [$fieldName => $resultImage['file']]);
                        }
                    }

                    $formId = $formData['form_id'];
                    $widgetName = $formData['form_name'];

                    $formDataModel = $this->formDataFactory->create();
                    $formDataModel->setData([
                        'form_id' => $formId,
                        'form_name' => $widgetName,
                        'form_data' => json_encode($formData),
                    ]);
                    $formDataModel->save();

                    if ($this->dolphinHelper->isEnableSender()) {
                        $this->sendEmail($formId, $widgetName, $formData);
                    }
                    $thankyouMessage = $formData['thankyou_message'];
                    $result->setData(['success' => true, 'message' => __($thankyouMessage)]);
                    return $result;
                }
            } catch (\Exception $e) {
                $result->setData(['success' => false, 'message' => __($e->getMessage())]);
                return $result;
            }
        }

        // Redirect to the referrer or a default page if the referrer is not available
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl() ?: $this->_redirect->getHomePageUrl());
        return $resultRedirect;
    }

    protected function sendEmail($formId, $widgetName, $formData)
    {
        $recipientName = $this->dolphinHelper->getRecipientName();
        $recipientEmail = $this->dolphinHelper->getRecipientEmail();
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
