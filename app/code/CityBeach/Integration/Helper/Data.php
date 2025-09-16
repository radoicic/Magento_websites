<?php
/**
 *
 */

namespace CityBeach\Integration\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_storeManager;

    public function __construct(Context $context, StoreManagerInterface $storeManager)
    {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
    }

    public function getStoreUrl()
    {
        $storeId = $this->_storeManager->getDefaultStoreView()->getStoreId();
        $url = $this->_storeManager->getStore($storeId)->getUrl();
        return $url;
    }
}
