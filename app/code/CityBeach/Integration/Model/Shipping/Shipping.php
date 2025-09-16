<?php
/**
 *
 */

namespace CityBeach\Integration\Model\Shipping;


class Shipping
    extends \Magento\Shipping\Model\Carrier\AbstractCarrier
    implements \Magento\Shipping\Model\Carrier\CarrierInterface
{
    protected $_code = 'citybeachshipping';
    protected $_name = 'Shipping';

    /*
     * @var \Magento\Framework\Registry $registryModel
     */
    private $registryModel;

    /*
     * @var \Magento\Framework\ObjectManagerInterface $objectManager
     */
    protected $resultFactory;
    /*
     * @var \Magento\Framework\ObjectManagerInterface $objectManager
     */
    protected $rateRequestFactory;
    /*
     * @var \Magento\Framework\ObjectManagerInterface $objectManager
     */
    protected $rateResultMethodFactory;


    public function __construct(
        \Magento\Framework\Registry $registryModel,
        \Magento\Quote\Model\Quote\Address\RateRequestFactory $rateRequestFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateResultMethodFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $resultFactory,
        array $data = []
    )
    {
        $this->registryModel = $registryModel;
        $this->resultFactory = $resultFactory;
        $this->rateRequestFactory = $rateRequestFactory;
        $this->rateResultMethodFactory = $rateResultMethodFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    public function collectRates(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        /* @var \CityBeach\Integration\Api\Data\ShippingInterface $shipping_detail */
        $shipping_detail =  $this->registryModel->registry(\CityBeach\Integration\Model\OrderBuilder::SHIPPING_DATA_KEY);
        if ($shipping_detail) {
            $result = $this->resultFactory->create();
            $method = $this->rateResultMethodFactory->create();

            $method->setCarrier($this->_code);
            $method->setMethod($this->_code);

            $method->setCarrierTitle($shipping_detail->getCarrierTitle());
            $method->setMethodTitle($shipping_detail->getMethodTitle());

            $method->setCost($shipping_detail->getPrice());
            $method->setPrice($shipping_detail->getPrice());

            $result->append($method);

            return $result;
        }
        else {
            return false;
        }
    }

    public function checkAvailableShipCountries(\Magento\Framework\DataObject $request)
    {
        return $this;
    }

    public function getErrorMessage()
    {
        return null;
    }

    public function getAllowedMethods()
    {
        return [$this->_code => $this->_name];
    }

    public function isTrackingAvailable()
    {
        return true;
    }

}
