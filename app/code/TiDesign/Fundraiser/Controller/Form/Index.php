<?php

namespace TiDesign\Fundraiser\Controller\Form;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\StoreManagerInterface;
use TiDesign\Fundraiser\Helper\Config as ConfigHelper;

class Index implements HttpGetActionInterface
{
    /**
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        protected ResultFactory $resultFactory,
    ) {
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_FORWARD)->forward('landing');
    }
}
