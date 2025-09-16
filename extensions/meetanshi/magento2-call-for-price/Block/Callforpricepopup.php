<?php

namespace Meetanshi\Callforprice\Block;

use Magento\Customer\Model\Session;
use Magento\Directory\Block\Data;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Callforpricepopup
 */
class Callforpricepopup extends Template
{
    /**
     * @var Data
     */
    private $directoryBlock;
    /**
     * @var bool
     */
    private $isScopePrivate;
    /**
     * @var Session
     */
    private $customerSession;
    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * Callforpricepopup constructor.
     * @param Data $directoryBlock
     * @param Context $context
     * @param HttpContext $httpContext
     * @param Session $session
     * @param array $data
     */
    public function __construct(
        Data $directoryBlock,
        Context $context,
        HttpContext $httpContext,
        Session $session,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->isScopePrivate = true;
        $this->directoryBlock = $directoryBlock;
        $this->customerSession = $session;
        $this->httpContext = $httpContext;
    }

    /**
     * @return bool
     */
    public function customerLoggedIn()
    {
        return (bool)$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    /**
     * @return string
     */
    public function getAvailableCountries()
    {
        $country = $this->directoryBlock->getCountryHtmlSelect();
        return $country;
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getLogginCustomerName()
    {
        if ($this->customerLoggedIn()) {
            $customer = $this->customerSession->getCustomer();
            return $customer->getName();
        } else {
            return '';
        }
    }

    /**
     * @return string
     */
    public function getLogginCustomerEmail()
    {
        if ($this->customerLoggedIn()) {
            return $this->customerSession->getCustomer()->getEmail();
        } else {
            return '';
        }
    }
}
