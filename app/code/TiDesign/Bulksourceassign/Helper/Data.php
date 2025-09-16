<?php
namespace TiDesign\Bulksourceassign\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\InventoryCatalogAdminUi\Model\BulkSessionProductsStorage;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;

use Magento\InventoryCatalogAdminUi\Model\GetSourceItemsDataBySku;

class Data extends AbstractHelper
{
	private $bulkSessionProductsStorage;
	private $sourceItemsBySku;

	 private $getSourceItemsDataBySku;
	
    public function __construct(
		Context $context,
		BulkSessionProductsStorage $bulkSessionProductsStorage,
		GetSourceItemsBySkuInterface $sourceItemsBySku,
GetSourceItemsDataBySku $getSourceItemsDataBySku
    ) {
		parent::__construct($context);
        $this->bulkSessionProductsStorage = $bulkSessionProductsStorage;
		$this->sourceItemsBySku = $sourceItemsBySku;
$this->getSourceItemsDataBySku = $getSourceItemsDataBySku;
    }	
	
	public function getProductsSkus()
    {
        return $this->bulkSessionProductsStorage->getProductsSkus();
    }
	
	public function getSourceItemBySku($sku)
    {
       return $this->sourceItemsBySku->execute($sku);
    }
	
	public function getData($sku){
		return $this->getSourceItemsDataBySku->execute($sku);
	}
	
}