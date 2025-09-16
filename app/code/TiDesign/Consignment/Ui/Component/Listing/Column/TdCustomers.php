<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace TiDesign\Consignment\Ui\Component\Listing\Column;

use Magento\Framework\Escaper;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Api\GroupRepositoryInterface;

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
    protected $_customer;
    protected $_customerFactory;
    protected $_backendUrl;
	protected $groupRepository;
	protected $scopeConfig;

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
        GroupRepositoryInterface $groupRepository,
		ScopeConfigInterface $scopeConfig,
		UrlInterface $backendUrl,
        array $components = [],
        array $data = []
    ) {
        $this->escaper = $escaper;
        $this->_customerFactory = $customerFactory;
        $this->_customer = $customers;
		$this->_backendUrl = $backendUrl;
		$this->groupRepository = $groupRepository;
		$this->scopeConfig = $scopeConfig;
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
				$content = '';
                if($item['td_customer']){

					$data = $this->prepareItem($item['td_customer']);



					foreach ($data as $cus) {

						$code 	= $this->groupRepository->getById($cus->getGroupId())->getCode();
						$consUserGroup	= $this->getConfig("consignment/consignment_status/customer_group");
						$consUserGroups	= explode(",", $this->getConfig("consignment/consignment_status/customer_groups"));

						$content .="<a href='".$this->_backendUrl->getUrl('customer/index/edit', ['id' => $cus->getId()])."' target='_blank'>";
						$content .= "<strong>". $cus->getName()."</strong><br> <i>(".$cus->getEmail().")</i> <span class='fa fa-fw fa-external-link'></span></a><br>Group: ";
						if( $cus->getGroupId() == $consUserGroup || in_array($cus->getGroupId(), $consUserGroups)){
							$content .= '<span class="txt-green">'.$code.'</span>';
						}else{
							$content .= '<span class="txt-red">'.$code.'</span>';
						}
					}
				}
				$item[$this->getData('name')] = $content;
            }
			return $dataSource;
        }

        return $dataSource;
    }
    /**
     * Get data
     *
     * @param array $item
     * @return string
     */
    protected function prepareItem($id)
    {
        return $this->_customerFactory->create()->getCollection()
                ->addAttributeToSelect("*")
                ->addAttributeToFilter("entity_id", array("eq" => $id))
                ->load();
    }
	public function getConfig($config_path)
	{
		return $this->scopeConfig->getValue(
			$config_path,
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
	}
}
