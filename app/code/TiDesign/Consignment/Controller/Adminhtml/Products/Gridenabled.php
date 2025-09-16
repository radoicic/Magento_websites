<?php
namespace TiDesign\Consignment\Controller\Adminhtml\Products;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\LayoutFactory;

class Gridenabled extends Action
{
	protected $resultRawFactory;
	protected $layoutFactory;
	
    public function __construct(
        Context $context,
        Rawfactory $resultRawFactory,
        LayoutFactory $layoutFactory
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
    }
	
    public function execute()
    {
        $resultRaw = $this->resultRawFactory->create();
        $blogHtml = $this->layoutFactory->create()->createBlock(
            'TiDesign\Consignment\Block\Adminhtml\Products\Enabledproducts',
            'new_gridenabled.view.grid'
        )->toHtml();
        return $resultRaw->setContents($blogHtml);
    }
}