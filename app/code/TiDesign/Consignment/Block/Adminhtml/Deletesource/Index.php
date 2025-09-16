<?php
namespace TiDesign\Consignment\Block\Adminhtml\Deletesource;

use Magento\Backend\Block\Template;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;

class Index extends Template
{
	private 	$scopeConfig;
	protected 	$categoryCollectionFactory;
	private 	$_consCollection;
	protected 	$_cusCol;
	private 	$sourceRepository;
	protected 	$_countryCollectionFactory;
    /**
    * @param Context $context
    * @param array $data
    */
    public function __construct(
        Template\Context $context,
		\TiDesign\Consignment\Model\ConsignmentlistFactory $consCollection,
		ScopeConfigInterface $scopeConfig,
		\Magento\Catalog\Model\CategoryRepository $categoryCollectionFactory,
		SourceRepositoryInterface $sourceRepository,
		\Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
		\Magento\Customer\Model\ResourceModel\Grid\CollectionFactory $cusCol,
        array $data = []
    ) {
		$this->_consCollection 	= $consCollection;
		$this->categoryCollectionFactory = $categoryCollectionFactory;
		$this->scopeConfig 		= $scopeConfig;
		$this->sourceRepository = $sourceRepository;
		$this->_countryCollectionFactory = $countryCollectionFactory;
        $this->_cusCol = $cusCol;
        parent::__construct($context, $data);
    }

	public function getSources(){
		$options = [];
		try {
			$sourceData = $this->sourceRepository->getList()->getItems();
			foreach ($sourceData as $source) {
				$options[] = ['label' => $source->getName(), 'value' => $source->getSourceCode()];
			}
			
		}catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
		return $options;
	}
    public function getConfig($config_path)
    {
        return $this->_scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}