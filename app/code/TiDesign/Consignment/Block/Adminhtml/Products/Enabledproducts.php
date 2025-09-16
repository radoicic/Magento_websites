<?php
namespace TiDesign\Consignment\Block\Adminhtml\Products;

class Enabledproducts extends \Magento\Backend\Block\Widget\Container
{
	protected $_template = 'products/grid_enabled.phtml';
	private 	$consignmentHelper;
	
	public function __construct(
		\TiDesign\Consignment\Helper\Consignment $consignmentHelper,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
		$this->consignmentHelper = $consignmentHelper;
        parent::__construct($context, $data);
    }
	
    protected function _prepareLayout()
    {

        $this->setChild('gridenabled', $this->getLayout()->createBlock('TiDesign\Consignment\Block\Adminhtml\Products\Grid\Gridenabled', 'gridenabled.view.grid'));
        $this->setChild('griddisabled', $this->getLayout()->createBlock('TiDesign\Consignment\Block\Adminhtml\Products\Grid\Griddisabled', 'griddisabled.view.grid'));
        return parent::_prepareLayout();
    }	
	
    public function getGridHtml()
    {
        return $this->getChildHtml('gridenabled');
    }
	
    public function getGridDisabledHtml()
    {
        return $this->getChildHtml('griddisabled');
    }
}