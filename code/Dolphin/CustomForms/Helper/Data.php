<?php

namespace Dolphin\CustomForms\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    const XML_PATH_EMAIL_RECIPIENT_NAME = 'trans_email/ident_general/name';
    const XML_PATH_EMAIL_RECIPIENT_EMAIL = 'trans_email/ident_general/email';
    const SEND_EMAIL_TO_ADMIN = 'customforms/general/send_email_to_admin';

    /**
     * @var $scopeConfig
     */
    protected $scopeConfig;
    /**
     * @var $storeManager
     */
    protected $storeManager;

    /**
     * Dolphin Helper
     *
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    )
    {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    public function getRecipientName()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT_NAME);
    }

    public function getRecipientEmail()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT_EMAIL);
    }

    public function isEnableSender()
    {
        return $this->scopeConfig->getValue(self::SEND_EMAIL_TO_ADMIN);
    }
}
