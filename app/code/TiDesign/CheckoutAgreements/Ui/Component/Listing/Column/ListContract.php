<?php

namespace TiDesign\CheckoutAgreements\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class ListContract extends Column
{
	private $_urlBuilder;
	private $_timezone;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param TimezoneInterface $timezone
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
		TimezoneInterface $timezone,
		UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->uiComponentFactory = $uiComponentFactory;
		$this->_timezone = $timezone;
        parent::__construct($context, $uiComponentFactory, $components, $data);
		$this->_urlBuilder = $urlBuilder;
    }

    public function prepareDataSource(array $dataSource)
    {
		$content = '';

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
				$content = "";
				// operation
				//	0-yok 1-accept/deny contract 2-allow/deny edit
				$status 		= ($item['status']) ?? 0;
				$edit 			= ($item['edit']) ?? 0;
				$activation 	= ($item['activation']) ?? 0;
				$contract_date	= ($item['contract_date']) ? $this->_timezone->date(new \DateTime($item['contract_date']))->format('M d, Y') : "-";
				$log_message	= ($item['log_message']) ?	1 : 0;
				$operation		= $item['operation'];

				if($item['filename']){
					//$application_for = json_decode($item['form_data'], true)['application_for'];
					$content .= "<p class='green'>Contract created at ".$contract_date."</p>";
					//$content .= "<p><b>Application for: </b>".(($application_for) ?? 'NOT FILLED!!!')."</p>";
					$mediaPath 	= $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);
					$filePath 	= $mediaPath . 'TiDesign/contracts/'.$item['filename'];
					$content 	.= "<a class='pdf-file'  target='_blank' type='application/pdf' href='".$filePath."'><i class='fa fa-file-pdf-o' aria-hidden='true'></i> ".$item['filename']."</a><br><br>";
					if($edit ==1){
						if($log_message == null ){
							$content .= "<p class='orange'>User's edit request accepted. New form awaited.</p>";
						}else{
							$content .= "<p class='orange'>User's form rejected. New form awaited.</p>";
						}
					}else{
						if($status == 1){
							$content .= "<p class='green'>Admin accepted contract</p>";
							if($activation == 1){
								$content .= "<p class='green'>User activated contract</p>";
								if($operation == 2){
									$content .= "<p class='red'>User requested Edit</p>
										<div>
										 <a class='td-grid-button button-accept accept-edit' data-customer='".$item['customer_id']."' data-log='".$item['log_id']."'>Accept</a>
										 <a data-src='#hidden-form' class='td-grid-button button-deny deny-edit' data-customer='".$item['customer_id']."' data-log='".$item['log_id']."'>Reject</a>
										 </div>";
								}
							}else{
								$content .= "<p class='orange'>Waiting user to activate contract</p>
												<a data-src='#hidden-form' class='td-grid-button button-accept manual-activate' data-customer='".$item['customer_id']."' data-log='".$item['log_id']."'>Activate by Admin</a>";
							}
						}else{
							$content .= "<p class='red'>User submited Contract please check and Accept/Deny form</p>
											<div>
											<a class='td-grid-button button-accept accept-contract' data-customer='".$item['customer_id']."' data-log='".$item['log_id']."'>Accept</a>
											<a data-src='#hidden-form' data-src='#hidden-form' class='td-grid-button button-deny deny-contract' data-customer='".$item['customer_id']."' data-log='".$item['log_id']."'>Reject</a>
											</div>";
						}
					}

				}else{
					$content	.= "<p class='orange'>T&C Form not filled yet...</p>";
				}
				$item[$this->getData('name')] =$content;
            }
        }
        return $dataSource;
    }
}
