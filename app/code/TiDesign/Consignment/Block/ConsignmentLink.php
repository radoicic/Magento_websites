<?php
namespace TiDesign\Consignment\Block;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Api\CustomerRepositoryInterface;

class ConsignmentLink extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * @var \Magento\Framework\App\Http\Context
     */
    private $httpContext;

	private $consignmentHelper;
	private $customerSession;
	private $customerRepository;	
    /**
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
		\TiDesign\Consignment\Helper\Consignment $consignmentHelper,
		CustomerRepositoryInterface $customerRepository,
		CustomerSession $customerSession,		
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->httpContext = $httpContext;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
		$this->consignmentHelper = $consignmentHelper;
		$this->customerRepository = $customerRepository;
		$this->customerSession = $customerSession;		
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
		if (false != $this->getTemplate()) {
			return parent::_toHtml();
		}
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customerSession = $objectManager->create("Magento\Customer\Model\Session");
		if($customerSession->isLoggedIn()){
			$customerId = $customerSession->getCustomerId();
			$url 		= $this->consignmentHelper->getPrivateLink($customerId);
			if($url){
				return '<li><a href="'.$url.'" >Consignment stock</a></li>';
			}
			return false;
		}else{
			return false;
		}

    }
}
