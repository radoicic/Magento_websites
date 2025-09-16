<?php

namespace CityBeach\Integration\Model;

use CityBeach\Integration\Api\ProductSummaryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Product;

class ProductSummary implements ProductSummaryInterface
{
    // Layers of stock query interface across Magento versions 2.1 - 2.3
    private $stockState;
    private $stockRegistry;
    // Stock interfaces used after to 2.3.
    private $hasInventorySalesApi;
    private $stockResolver;
    private $getProductSalableQty;
    private $isProductSalable;
    private $sourceItemRepository;
    private $sourceRepository;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    private $productHelper;

    /**
     * @var \Magento\Catalog\Model\Product\Gallery\ReadHandler
     */
    private $galleryReadHandler;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Store\Api\Data\WebsiteInterface
     */
    private $website;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Catalog\Model\Product\Gallery\ReadHandler $galleryReadHandler
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Store\Api\Data\WebsiteInterface $website
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\Product\Gallery\ReadHandler $galleryReadHandler,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Api\Data\WebsiteInterface $website,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->logger = $logger;
        $this->productRepository = $productRepository;
        $this->productHelper = $productHelper;
        $this->galleryReadHandler = $galleryReadHandler;
        $this->storeManager = $storeManager;
        $this->website = $website;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;

        // Dynamic look up to handle changing interface between Magento 2.1 and later.

        // Deprecated: magento 2.3.0
        $this->stockState = interface_exists('\Magento\CatalogInventory\Api\StockStateInterface') ? $objectManager->create('\Magento\CatalogInventory\Api\StockStateInterface') : FALSE;
        // Deprecated: magento 2.3.0
        $this->stockRegistry = interface_exists('\Magento\CatalogInventory\Api\StockRegistryInterface') ? $objectManager->create('\Magento\CatalogInventory\Api\StockRegistryInterface') : FALSE;

        /* @var $moduleManager \Magento\Framework\Module\Manager */
        $moduleManager = $objectManager->create('\Magento\Framework\Module\Manager');
        if ($moduleManager->isEnabled('Magento_InventorySalesApi')) {
            $this->hasInventorySalesApi = TRUE;
            $this->stockResolver = $objectManager->create('\Magento\InventorySalesApi\Api\StockResolverInterface');
            $this->getProductSalableQty = $objectManager->create('\Magento\InventorySalesApi\Api\GetProductSalableQtyInterface');
            $this->isProductSalable = $objectManager->create('\Magento\InventorySalesApi\Api\IsProductSalableInterface');
            $this->sourceItemRepository = $objectManager->create('\Magento\InventoryApi\Api\SourceItemRepositoryInterface');
            $this->sourceRepository = $objectManager->create('\Magento\InventoryApi\Api\SourceRepositoryInterface');
        }
        else {
            $this->hasInventorySalesApi = FALSE;
        }
    }

    /**
     * Load Block data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @param string $sourceCode
     * @return array
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria, $sourceCode)
    {
        $websiteCode = $this->extractWebsiteCode($criteria);
        $products = $this->productRepository->getList($criteria);
        $productSummaries = array();

        $sourceCodes = $this->convertToSources($sourceCode);

        /* @var $product ProductInterface */
        foreach ($products->getItems() as $product) {
          // filling the product details
          $id = $product->getId();
          $typeInstance = $product->getTypeInstance();
          $productSummary = array(
                'id' => $id,
                'sku' => $product->getSku(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'weight' => $product->getWeight(),
                'status' => $product->getStatus(),
                'visibility' => $product->getVisibility(),
                'type' => $product->getTypeId(),
                'created' => $product->getCreatedAt(),
                'updated' => $product->getUpdatedAt(),
                'category_ids' => $product->getCategoryIds(),
            );
            $attributes = array();
            foreach ($product->getData() as $key => $value) {
                if (!empty($value) || strpos($key, '_') !== 0) {
                    switch ($key) {
                        case 'image':
                            $attributes[$key] = $this->productHelper->getImageUrl($product);
                            break;

                        case 'small_image':
                            $attributes[$key] = $this->productHelper->getSmallImageUrl($product);
                            break;

                        case 'thumbnail':
                            $attributes[$key] = $this->productHelper->getThumbnailUrl($product);
                            break;

                        default:
                            $attributes[$key] = $value;
                            break;
                    }
                }
            }

            // attaching the media gallery
            $this->galleryReadHandler->execute($product);
            // access the gallery
            $images = [];
            $gallery = $product->getMediaGalleryImages()->getItems();
            foreach ($gallery as $image) {
              $images[] = $image->getUrl();
            }
            $attributes['gallery'] = $images;

            $productSummary['attributes'] = $attributes;
            if ($product->getTypeId() === 'configurable') {
                $used = $typeInstance->getUsedProducts($product);
                $related = array();
                foreach ($used as $use) {
                    $related[] = $use->getId();
                }
                $productSummary['related'] = $related;
            }
            elseif ($product->getTypeId() === 'grouped') {
                $associatedIds = $typeInstance->getAssociatedProductIds($product);
                $related = array();
                foreach ($associatedIds as $associatedId) {
                    $related[] = $associatedId;
                }
                $productSummary['related'] = $related;
            }
            elseif ($product->getTypeId() === 'bundle') {
                $childrenIds = $typeInstance->getChildrenIds($product->getId());
                $related = array();
                foreach ($childrenIds as $childId) {
                    $related[] = $childId;
                }
                $productSummary['related'] = $related;
            }

            if (!empty($sourceCodes)) {
                $inventory = $this->queryInventory($product, $sourceCodes);
                $productSummary['inventory'] = $inventory;
            }

            $stock = $this->queryStock($product, $websiteCode);
            $productSummary['stock'] = $stock['qty'];
            if (array_key_exists('stock_status', $stock)) {
                $productSummary['stock_status'] = $stock['stock_status'];
            }

            $productSummary['link'] = $this->productHelper->getProductUrl($product);

            $productSummaries[$id] = $productSummary;
        }

        return $productSummaries;
    }

    /**
     * Retrieve product inventory for available sources for products matching the specified criteria.
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @param string $sourceCode
     * @return array
     */
    public function getSourceInventoryList(\Magento\Framework\Api\SearchCriteriaInterface $criteria, $sourceCode) {
        $sourceCodes = $this->convertToSources($sourceCode);

        if ($sourceCodes && $this->hasInventorySalesApi) {
            $sourceItemsData = [];
            $sourceItems = $this->sourceItemRepository->getList($criteria)->getItems();

            foreach ($sourceItems as $sourceItem) {
                if ( $sourceCodes === TRUE || in_array($sourceItem->getSourceCode(), $sourceCodes) ) {
                    $sourceItemsData[] = [
                        \Magento\InventoryApi\Api\Data\SourceItemInterface::SKU => $sourceItem->getSku(),
                        \Magento\InventoryApi\Api\Data\SourceItemInterface::SOURCE_CODE => $sourceItem->getSourceCode(),
                        \Magento\InventoryApi\Api\Data\SourceItemInterface::QUANTITY => $sourceItem->getQuantity(),
                        \Magento\InventoryApi\Api\Data\SourceItemInterface::STATUS => $sourceItem->getStatus(),
                    ];
                }
            }

            return $sourceItemsData;
        }
        else {
            return [];
        }
    }

    /**
     * Count product summary for products matching the specified criteria.
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return array
     */
    public function getCount(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        $products = $this->productRepository->getList($criteria);
        return [[
          'product_summary_count' => $products->getTotalCount(),
        ]];
    }

    private function extractWebsiteCode(\Magento\Framework\Api\SearchCriteriaInterface $criteria) {
      $websiteId = $this->storeManager->getStore()->getWebsiteId();
      $this->website->load($websiteId);
      $code = $this->website->getCode();
      return $code;
    }

    private function queryStock($product, $websiteCode) {
      $result = [];
      if ($this->stockResolver && $this->getProductSalableQty && defined ( '\Magento\InventorySalesApi\Api\Data\SalesChannelInterface::TYPE_WEBSITE' )) {
        if ($product->getTypeId() == 'simple') {
          $stockId = $this->stockResolver->execute(constant('\Magento\InventorySalesApi\Api\Data\SalesChannelInterface::TYPE_WEBSITE'), $websiteCode)->getStockId();
          $result['qty'] = (int)$this->getProductSalableQty->execute($product->getSku(), $stockId);
          $result['stock_status'] = (int)$this->isProductSalable->execute($product->getSku(), $stockId);
        }
        else {
          $result['qty'] = 0;
        }
      }
      else if ($this->stockRegistry) {
        $productStock = $this->stockRegistry->getStockItem($product->getId());
        $result['qty'] =  $productStock->getQty();
        $result['stock_status'] = $productStock->getIsInStock();
      }
      else {
        $result['qty'] = $this->stockState->getStockQty($product->getId(), $product->getStore()->getWebsiteId());
      }

      return $result;
    }

    private function queryInventory($product, $sourceCodes) {
        if ($sourceCodes && $this->hasInventorySalesApi) {
            $sourceItemsData = [];
            $searchCriteria = $this->searchCriteriaBuilder->addFilter(\Magento\InventoryApi\Api\Data\SourceItemInterface::SKU, $product->getSku())->create();

            $sourceItems = $this->sourceItemRepository->getList($searchCriteria)->getItems();
            foreach ($sourceItems as $sourceItem) {
                if ( $sourceCodes === TRUE || in_array($sourceItem->getSourceCode(), $sourceCodes) ) {
                    $sourceItemsData[] = [
                        \Magento\InventoryApi\Api\Data\SourceItemInterface::SKU => $sourceItem->getSku(),
                        \Magento\InventoryApi\Api\Data\SourceItemInterface::SOURCE_CODE => $sourceItem->getSourceCode(),
                        \Magento\InventoryApi\Api\Data\SourceItemInterface::QUANTITY => $sourceItem->getQuantity(),
                        \Magento\InventoryApi\Api\Data\SourceItemInterface::STATUS => $sourceItem->getStatus(),
                    ];
                }
            }

            return $sourceItemsData;
        }
        else {
            return [];
        }
    }

    /**
     * @param string $sourceCode
     * @return bool|false|string[]
     */
    public function convertToSources($sourceCode)
    {
        $sourceCodes = FALSE;
        if ($sourceCode && $sourceCode !== 'none') {
            if ($sourceCode === 'all') {
                $sourceCodes = TRUE;
            } else {
                $sourceCodes = explode(',', $sourceCode);
                if (empty($sourceCodes)) {
                    $sourceCodes = FALSE;
                }
            }
        }
        return $sourceCodes;
    }
}
