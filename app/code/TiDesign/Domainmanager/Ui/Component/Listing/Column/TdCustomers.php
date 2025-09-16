<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace TiDesign\Domainmanager\Ui\Component\Listing\Column;

use Magento\Framework\Escaper;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Backend\Model\UrlInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Customer;

/**
 * Class Store
 */
class TdCustomers extends Column
{
    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var string
     */
    protected $storeKey;

    protected $_customer;
    protected $_customerFactory;
    protected $_backendUrl;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Escaper $escaper
     * @param array $components
     * @param array $data
     * @param string $storeKey
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Escaper $escaper,
        CustomerFactory $customerFactory,
		Customer $customers,
		UrlInterface $backendUrl,
        array $components = [],
        array $data = [],
        $storeKey = 'td_store_id'
    ) {
        $this->escaper = $escaper;
        $this->storeKey = $storeKey;
        $this->_customerFactory = $customerFactory;
        $this->_customer = $customers;
		$this->_backendUrl = $backendUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = $this->prepareItem($item);
            }
        }

        return $dataSource;
    }

    /**
     * Get data
     *
     * @param array $item
     * @return string
     */
    protected function prepareItem(array $item)
    {
        $content = '';
        if (!empty($item[$this->storeKey])) {
			$data = $this->getFilteredCustomerCollection($item[$this->storeKey]);
			foreach ($data as $cus) {
				$content .="<a href='".$this->_backendUrl->getUrl('customer/index/edit', ['id' => $cus->getId()])."' target='_blank'>";
				$content .= "<strong>". $cus->getName()."</strong> <i>(".$cus->getEmail().")</i> <span class='fa fa-fw fa-external-link'></span></a>";
			}
			$content = str_replace("</a><a","</a><br/><a", $content);
		}
        return $content;
    }

    public function getFilteredCustomerCollection($key) {
        return $this->_customerFactory->create()->getCollection()
                ->addAttributeToSelect("*")
                ->addAttributeToFilter("consignment_active", array("eq" => 1))
                ->addAttributeToFilter("consignment_store", array("eq" => $key))
                ->load();
    }

}
