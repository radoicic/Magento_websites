<?php

namespace TiDesign\Fundraiser\Model\ThemeCustomer;

use Magento\Framework\Serialize\Serializer\Json;
use TiDesign\Fundraiser\Model\ResourceModel\ThemeCustomer as ResourceModel;

class SaveTheme
{
    /**
     * @param Json $serializer
     */
    public function __construct(
        protected Json $serializer,
        protected ResourceModel $resourceModel
    ) {
    }

    /**
     * @param \TiDesign\Fundraiser\Model\ThemeCustomer $themeCustomer
     * @return \TiDesign\Fundraiser\Model\ThemeCustomer
     */
    public function execute($themeCustomer)
    {
        $configData = $themeCustomer->getConfigData();
        if (is_array($configData)) {
            $themeCustomer->setConfigData($this->serializer->serialize($configData));
        }
        if (!$themeCustomer->getData('tracking_code')) {
            $customerId = $themeCustomer->getData('customer_id');
            $key = ($customerId ?: 0) . '_' . time();
            $code = md5($key);
            $themeCustomer->setData('tracking_code', $code);
        }
        $this->resourceModel->save($themeCustomer);
        return $themeCustomer;
    }
}
