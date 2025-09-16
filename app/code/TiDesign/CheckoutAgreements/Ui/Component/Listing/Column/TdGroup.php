<?php
namespace TiDesign\CheckoutAgreements\Ui\Component\Listing\Column;

use Magento\Framework\Escaper;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Api\GroupRepositoryInterface;

/**
 * Class Store
 */
class TdGroup extends Column
{

    protected $escaper;
    protected $_backendUrl;
	protected $groupRepository;
	protected $scopeConfig;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Escaper $escaper
     * @param GroupRepositoryInterface $groupRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param UrlInterface $backendUrl
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Escaper $escaper,
		GroupRepositoryInterface $groupRepository,
		ScopeConfigInterface $scopeConfig,
		UrlInterface $backendUrl,
        array $components = [],
        array $data = []
    ) {
        $this->escaper = $escaper;
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
				$content 	= '';
				$c			= [];
				$groups		= [];
                if($item['customer_group']){

					$groups =explode(',',$item['customer_group']);
					foreach($groups as $cg){
						if($cg == "32000"){
							$c[] = '<span>ALL GROUPS</span> ';
						}else{
							$code 	= $this->groupRepository->getById($cg)->getCode();
							$c[] = '<span>'.$code.'</span> ';
						}
					}
					$content = implode(",",$c).$item['customer_group'];
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

	public function getConfig($config_path)
	{
		return $this->scopeConfig->getValue(
			$config_path,
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
	}
}
