<?php

namespace TiDesign\Fundraiser\Block\Customer\Order;

class View extends \Magento\Sales\Block\Order\View
{
    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/customer/orders');
    }

    public function getBackTitle()
    {
        return __('Back to Fundraiser Orders');
    }
}
