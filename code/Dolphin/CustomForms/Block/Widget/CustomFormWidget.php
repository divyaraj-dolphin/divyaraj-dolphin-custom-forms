<?php

declare(strict_types=1);

namespace Dolphin\CustomForms\Block\Widget;

use Dolphin\CustomForms\Model\DolphinTable;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Widget\Block\BlockInterface;
use Psr\Log\LoggerInterface;

class CustomFormWidget extends \Magento\Framework\View\Element\Template implements BlockInterface, IdentityInterface
{
    protected $_template = "Dolphin_CustomForms::widget/customWidget.phtml";

    protected $_filterProvider;

    protected $_blockFactory;
    protected $logger;

    private $block;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Dolphin\CustomForms\Model\DolphinTableFactory $blockFactory,
        LoggerInterface $logger,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_filterProvider = $filterProvider;
        $this->_blockFactory = $blockFactory;
        $this->logger = $logger;
    }

    private function getBlockById(int $blockId): ?DolphinTable
    {
        try {
            $storeId = $this->_storeManager->getStore()->getId();
            /** @var DolphinTable $block */
            $block = $this->_blockFactory->create();
            $block->setStoreId($storeId)->load($blockId);
            return $block;
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();

        $formId = $this->getData('form_id');

        $form = $this->getBlockById((int)$formId);

        // Check if the form exists before proceeding
        if ($form) {
            // Get the form data
            $formData = $form->getData();

            // Add the form_id to the form data
            $formData['form_id'] = $formId;

            // Set the combined data to the widget
            $this->setData($formData);
        }

        return $this;
    }


    public function getIdentities()
    {
        $block = $this->getBlock();

        if ($block) {
            return $block->getIdentities();
        }

        return [];
    }

//    private function getBlock(): ?DolphinTable
//    {
//        if ($this->block) {
//            return $this->block;
//        }
//
//        $blockId = $this->getData('form_id');
//
//        if ($blockId) {
//            try {
//                $storeId = $this->_storeManager->getStore()->getId();
//                /** @var DolphinTable $block */
//                $block = $this->_blockFactory->create();
//                $block->setStoreId($storeId)->load($blockId);
//                $this->block = $block;
//
//                return $block;
//            } catch (NoSuchEntityException $e) {
//            }
//        }
//
//        return null;
//    }
}
