<?php

namespace TiDesign\CheckoutAgreements\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class TdOperation extends Column
{
	protected $urlBuilder;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
	
    public function prepareDataSource(array $dataSource)
    {


        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {	
				if($item['status']==0){
					if($item['operation']==0){
						$item[$this->getData('name')] = "";
					}
					if($item['operation']==1){
						$item[$this->getData('name')] = "<a class='td-grid-button button-accept accept-contract' data-customer='".$item['customer_id']."' data-log='".$item['log_id']."'>Accept</a> 
														 <a data-src='#hidden-form' class='td-grid-button button-deny deny-contract' data-customer='".$item['customer_id']."' data-log='".$item['log_id']."'>Reject</a>"; 
					}
					if($item['operation']==2){
						$item[$this->getData('name')] = "<a class='td-grid-button button-accept accept-edit' data-customer='".$item['customer_id']."' data-log='".$item['log_id']."'>Accept</a> 
														 <a data-src='#hidden-form' class='td-grid-button button-deny deny-edit' data-customer='".$item['customer_id']."' data-log='".$item['log_id']."'>Reject</a>"; 
					}				
				}					
				if($item['status']==1){
					$item[$this->getData('name')] = "<span class='msg-accept'><i aria-hidden='true' class='fa fa-check-circle-o'></i> Accepted</span>";
				}					
				if($item['status']==2){
					$item[$this->getData('name')] = "<span class='msg-deny'><i aria-hidden='true' class='fa fa-times '></i> Denied</span>";
				}					
				if($item['status']==3){
					$item[$this->getData('name')] = "";
				}
				
            }
        }




        return $dataSource;
    }
}