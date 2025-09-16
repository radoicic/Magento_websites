<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Ui\InventoryShipping\DataProvider;

/**
 * Source selection data provider plugin
 */
class SourceSelectionDataProvider
{
    /**
     * Get source by source code
     * 
     * @var \Ecombricks\InventoryCommon\Model\GetSourceBySourceCode
     */
    private $getSourceBySourceCode;
    
    /**
     * Get sources
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Ui\InventoryShipping\DataProvider\GetSources
     */
    private $getSources;
    
    /**
     * Order item wrapper factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order\ItemFactory 
     */
    private $orderItemWrapperFactory;
    
    /**
     * Request
     * 
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;
    
    /**
     * Get stock item configuration
     * 
     * @var \Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface
     */
    private $getStockItemConfiguration;

    /**
     * Get SKU from order item
     * 
     * @var \Magento\InventorySalesApi\Model\GetSkuFromOrderItemInterface
     */
    private $getSkuFromOrderItem;

    /**
     * Stock by website ID resolver
     * 
     * @var \Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface
     */
    private $stockByWebsiteIdResolver;

    /**
     * Order repository
     * 
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * Sources
     * 
     * @var array
     */
    private $sources = [];

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Model\GetSourceBySourceCode $getSourceBySourceCode
     * @param \Ecombricks\InventoryCustomerSourceSelection\Ui\InventoryShipping\DataProvider\GetSources $getSources
     * @param \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order\ItemFactory $orderItemWrapperFactory
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface $getStockItemConfiguration
     * @param \Magento\InventorySalesApi\Model\GetSkuFromOrderItemInterface $getSkuFromOrderItem
     * @param \Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface $stockByWebsiteIdResolver
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Model\GetSourceBySourceCode $getSourceBySourceCode,
        \Ecombricks\InventoryCustomerSourceSelection\Ui\InventoryShipping\DataProvider\GetSources $getSources,
        \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order\ItemFactory $orderItemWrapperFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface $getStockItemConfiguration,
        \Magento\InventorySalesApi\Model\GetSkuFromOrderItemInterface $getSkuFromOrderItem,
        \Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface $stockByWebsiteIdResolver,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    )
    {
        $this->getSourceBySourceCode = $getSourceBySourceCode;
        $this->getSources = $getSources;
        $this->orderItemWrapperFactory = $orderItemWrapperFactory;
        $this->request = $request;
        $this->getStockItemConfiguration = $getStockItemConfiguration;
        $this->getSkuFromOrderItem = $getSkuFromOrderItem;
        $this->stockByWebsiteIdResolver = $stockByWebsiteIdResolver;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Around get data
     * 
     * @param \Magento\InventoryShippingAdminUi\Ui\DataProvider\SourceSelectionDataProvider $subject
     * @param \Closure $proceed
     * @return array
     */
    public function aroundGetData(
        \Magento\InventoryShippingAdminUi\Ui\DataProvider\SourceSelectionDataProvider $subject,
        \Closure $proceed
    ) : array
    {
        $data = [];
        $orderId = (int) $this->request->getParam('order_id');
        $order = $this->orderRepository->get($orderId);
        $websiteId = (int) $order->getStore()->getWebsiteId();
        $stockId = (int) $this->stockByWebsiteIdResolver->execute($websiteId)->getStockId();
        foreach ($order->getAllItems() as $orderItem) {
            if ($orderItem->getIsVirtual() || $orderItem->getLockedDoShip() || $orderItem->getHasChildren()) {
                continue;
            }
            $item = $orderItem->isDummy(true) ? $orderItem->getParentItem() : $orderItem;
            $itemId = (int) $item->getId();
            $sku = $this->getSkuFromOrderItem->execute($item);
            $qty = $this->castQty($item, $item->getSimpleQtyToShip());
            $data[$orderId]['items'][] = [
                'orderItemId' => $itemId,
                'sku' => $sku,
                'product' => $this->getProductName($orderItem),
                'qtyToShip' => $qty,
                'sources' => $this->getSources($orderId, $itemId, $sku, $qty),
                'isManageStock' => $this->isManageStock($sku, $stockId)
            ];
        }
        $data[$orderId]['websiteId'] = $websiteId;
        $data[$orderId]['order_id'] = $orderId;
        foreach ($this->sources as $code => $name) {
            $data[$orderId]['sourceCodes'][] = ['value' => $code, 'label' => $name];
        }
        return $data;
    }

    /**
     * Cast quantity
     * 
     * @param \Magento\Sales\Model\Order\Item $item
     * @param string|int|float $qty
     * @return float|int
     */
    private function castQty(\Magento\Sales\Model\Order\Item $item, $qty)
    {
        if ($item->getIsQtyDecimal()) {
            $qty = (double) $qty;
        } else {
            $qty = (int) $qty;
        }
        return $qty > 0 ? $qty : 0;
    }
    
    /**
     * Get product name
     * 
     * @param \Magento\Sales\Model\Order\Item $item
     * @return string
     */
    private function getProductName(\Magento\Sales\Model\Order\Item $item): string
    {
        $name = $item->getName();
        $parentItem = $item->getParentItem();
        if ($parentItem) {
            $name = $parentItem->getName();
            $options = [];
            $productOptions = $parentItem->getProductOptions();
            if ($options) {
                if (isset($productOptions['options'])) {
                    $options = array_merge($options, $productOptions['options']);
                }
                if (isset($productOptions['additional_options'])) {
                    $options = array_merge($options, $productOptions['additional_options']);
                }
                if (isset($productOptions['attributes_info'])) {
                    $options = array_merge($options, $productOptions['attributes_info']);
                }
                if (count($options)) {
                    foreach ($options as $option) {
                        $name .= '<dd>'.$option['label'].': '.$option['value'].'</dd>';
                    }
                } else {
                    $name .= '<dd>'.$item->getName().'</dd>';
                }
            }
        }
        $sourceCode = (string) $this->orderItemWrapperFactory->create($item)->getSourceCode();
        if (!empty($sourceCode)) {
            $source = $this->getSourceBySourceCode->execute($sourceCode);
            $name .= '<dd><strong>'.__('Source').':</strong> '.($source ? $source->getName() : $sourceCode).'</dd>';
        }
        return $name;
    }
    
    /**
     * Get sources
     * 
     * @param int $orderId
     * @param int $orderItemId
     * @param string $sku
     * @param float $qty
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getSources(int $orderId, int $orderItemId, string $sku, float $qty): array
    {
        $sources = $this->getSources->execute($orderId, $orderItemId, $sku, $qty);
        foreach ($sources as $source) {
            $this->sources[$source['sourceCode']] = $source['sourceName'];
        }
        return $sources;
    }
    
    /**
     * Check if is manage stock
     * 
     * @param string $itemSku
     * @param int $stockId
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function isManageStock(string $itemSku, int $stockId): int
    {
        $stockItemConfiguration = $this->getStockItemConfiguration->execute($itemSku, $stockId);
        return (int) $stockItemConfiguration->isManageStock();
    }
}