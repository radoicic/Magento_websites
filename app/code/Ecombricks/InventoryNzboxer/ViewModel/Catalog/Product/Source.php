<?php

/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryNzboxer\ViewModel\Catalog\Product;

use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;

class Source extends \Magento\Framework\DataObject implements \Magento\Framework\View\Element\Block\ArgumentInterface
{

    private $sourceItemsBySku;
    protected $_domainCollectionFactory;
    protected $_domainHelper;
    protected $_catalogHelper;

    /**
     * @param \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $sourceItemsBySku
     * @param \TiDesign\Domainmanager\Model\ResourceModel\Domainlist\CollectionFactory $domainCollectionFactory
     */
    public function __construct(
        \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $sourceItemsBySku,
        \TiDesign\Domainmanager\Helper\Data $domainHelper,
        \Magento\Catalog\Helper\Data $catalogHelper,
		SourceRepositoryInterface $sourceRepository
    ) {
        $this->sourceItemsBySku = $sourceItemsBySku;
        $this->_domainHelper = $domainHelper;
        $this->_catalogHelper = $catalogHelper;
		$this->sourceRepository = $sourceRepository;
    }

    public function getSourceItemBySku(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        $sources = [];
        $sourceItemList = $this->sourceItemsBySku->execute($product->getData(\Magento\Catalog\Api\Data\ProductInterface::SKU));
        foreach ($sourceItemList as $source) {
            $data = $source->getData();
            $code = $data["source_code"];
            $sources[$code]["code"] = $data["source_code"];
            $sources[$code]["qty"] = $data["quantity"];
            $sources[$code]["status"] 	= $data["status"];
        }
        return $sources;
    }
    public function getInventoryItemBySku(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        $sources = [];
        $sourceItemList = $this->sourceItemsBySku->execute($product->getData(\Magento\Catalog\Api\Data\ProductInterface::SKU));
        foreach ($sourceItemList as $source) {
            $data = $source->getData();
			if($data["quantity"]>0){
				$code = $data["source_code"];
				$sources[$code]["code"] 	= $data["source_code"];
				$sources[$code]["qty"] 		= $data["quantity"];
				$sources[$code]["status"] 	= $data["status"];
				$sourceInfo = $this->sourceRepository->get($data["source_code"]);
				$sources[$code]["name"] 	= $sourceInfo["name"];
			}
        }
        return $sources;
    }

    public function getDomainInventorySource()
    {
        $Url = $_SERVER['HTTP_HOST'];
        $data = $this->_domainHelper->getCollectionData($Url);
        $m = [];
        $currentCategory = null;
        if ($this->_catalogHelper->getCategory()){
            $currentCategory = $this->_catalogHelper->getCategory()->getId();
        }
        if ($data){
            $m["store_id"] = $data->getTdStore();
            $m["consignment"] = $data->getTdConsignment();
            $m["source"] = $data->getTdSource();
            $m["default_source"] = $this->_domainHelper->getConfig("domainmanager/domainmanager_status/default_source");
            $m["category"] = $data->getTdCategory();
            $m["cur_category"] = $currentCategory;
        }
        return $m;
    }
}