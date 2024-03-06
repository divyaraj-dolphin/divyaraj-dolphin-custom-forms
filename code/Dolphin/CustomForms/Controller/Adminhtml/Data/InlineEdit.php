<?php

namespace Dolphin\CustomForms\Controller\Adminhtml\Data;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Dolphin\CustomForms\Model\DolphinTableFactory;

class InlineEdit extends \Magento\Backend\App\Action
{
    protected $jsonFactory;
    private $dataFactory;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        DolphinTableFactory $dataFactory
    )
    {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->dataFactory = $dataFactory;
    }

    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $modelid) {
                    $model = $this->dataFactory->create()->load($modelid);
                    try {
                        $model->setData(array_merge($model->getData(), $postItems[$modelid]));
                        $model->save();
                    } catch (\Exception $e) {
                        $messages[] = "[Customform ID: {$modelid}] {$e->getMessage()}";
                        $error = true;
                    }
                }
            }
        }


        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error]);
    }
}
