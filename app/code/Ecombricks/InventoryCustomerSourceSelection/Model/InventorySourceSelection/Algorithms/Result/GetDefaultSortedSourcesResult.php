<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\InventorySourceSelection\Algorithms\Result;

/**
 * Get default sorted sources result
 */
class GetDefaultSortedSourcesResult
{
    /**
     * Order item wrapper factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order\ItemFactory 
     */
    private $orderItemWrapperFactory;

    /**
     * Search criteria builder
     * 
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * Product metadata
     * 
     * @var \Magento\Framework\App\ProductMetadata 
     */
    private $productMetadata;

    /**
     * Source item repository
     * 
     * @var \Magento\InventoryApi\Api\SourceItemRepositoryInterface
     */
    private $sourceItemRepository;

    /**
     * Source selection item factory
     * 
     * @var \Magento\InventorySourceSelectionApi\Api\Data\SourceSelectionItemInterfaceFactory
     */
    private $sourceSelectionItemFactory;
    
    /**
     * Source selection result factory
     * 
     * @var \Magento\InventorySourceSelectionApi\Api\Data\SourceSelectionResultInterfaceFactory
     */
    private $sourceSelectionResultFactory;

    /**
     * Order item repository
     * 
     * @var \Magento\Sales\Api\OrderItemRepositoryInterface
     */
    private $orderItemRepository;

    /**
     * Get source item qty available
     * 
     * @var \Magento\InventorySourceSelectionApi\Model\GetSourceItemQtyAvailableInterface
     */
    private $getSourceItemQtyAvailable;

    /**
     * Source items
     * 
     * @var \Magento\InventoryApi\Api\Data\SourceItemInterface[][]
     */
    private $sourceItems = [];

    /**
     * Source item quantities
     * 
     * @var array
     */
    private $sourceItemQuantities = [];

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order\ItemFactory $orderItemWrapperFactory
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\App\ProductMetadata $productMetadata
     * @param \Magento\InventoryApi\Api\SourceItemRepositoryInterface $sourceItemRepository
     * @param \Magento\InventorySourceSelectionApi\Api\Data\SourceSelectionItemInterfaceFactory $sourceSelectionItemFactory
     * @param \Magento\InventorySourceSelectionApi\Api\Data\SourceSelectionResultInterfaceFactory $sourceSelectionResultFactory
     * @param \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order\ItemFactory $orderItemWrapperFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\ProductMetadata $productMetadata,
        \Magento\InventoryApi\Api\SourceItemRepositoryInterface $sourceItemRepository,
        \Magento\InventorySourceSelectionApi\Api\Data\SourceSelectionItemInterfaceFactory $sourceSelectionItemFactory,
        \Magento\InventorySourceSelectionApi\Api\Data\SourceSelectionResultInterfaceFactory $sourceSelectionResultFactory,
        \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository
    )
    {
        $this->orderItemWrapperFactory = $orderItemWrapperFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productMetadata = $productMetadata;
        $this->sourceItemRepository = $sourceItemRepository;
        $this->sourceSelectionItemFactory = $sourceSelectionItemFactory;
        $this->sourceSelectionResultFactory = $sourceSelectionResultFactory;
        $this->orderItemRepository = $orderItemRepository;
        if (version_compare($this->productMetadata->getVersion(), '2.3.2', '>=')) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->getSourceItemQtyAvailable = $objectManager->get(\Magento\InventorySourceSelectionApi\Model\GetSourceItemQtyAvailableInterface::class);
        }
    }

    /**
     * Execute
     *
     * @param \Magento\InventorySourceSelectionApi\Api\Data\InventoryRequestInterface $inventoryRequest
     * @param \Magento\InventoryApi\Api\Data\SourceInterface[] $sortedSources
     * @return \Magento\InventorySourceSelectionApi\Api\Data\SourceSelectionResultInterface
     */
    public function execute(
        \Magento\InventorySourceSelectionApi\Api\Data\InventoryRequestInterface $inventoryRequest,
        array $sortedSources
    ): \Magento\InventorySourceSelectionApi\Api\Data\SourceSelectionResultInterface
    {
        $isDefault = (bool) $inventoryRequest->getExtensionAttributes()->getIsDefault();
        $isShippable = true;
        $sourceItemSelections = [];
        foreach ($inventoryRequest->getItems() as $item) {
            $orderItemId = (int) $item->getExtensionAttributes()->getOrderItemId();
            $orderItem = $this->getOrderItem($orderItemId);
            $orderItemSourceCode = $orderItem ? $this->orderItemWrapperFactory->create($orderItem)->getSourceCode() : null;
            $sku = $this->normalizeSku($item->getSku());
            $qtyToDeliver = $item->getQty();
            foreach ($sortedSources as $source) {
                $sourceCode = $source->getSourceCode();
                if ($this->getInStockSourceItem($sku, $sourceCode) === null) {
                    continue;
                }
                $sourceItemQty = $this->getSourceItemQty($sku, $sourceCode);
                $qtyToDeduct = (!$isDefault || ($isDefault && $orderItemSourceCode === $sourceCode)) ? 
                    min($sourceItemQty, $qtyToDeliver) : 0;
                if ($this->isZero($sourceItemQty)) {
                    continue;
                }
                $sourceItemSelection = $this->sourceSelectionItemFactory->create([
                    'sourceCode' => $sourceCode,
                    'sku' => $sku,
                    'qtyToDeduct' => $qtyToDeduct,
                    'qtyAvailable' => $this->getSourceItemQtyAvailable($sku, $sourceCode),
                ]);
                $sourceItemSelection->getExtensionAttributes()->setOrderItemId($orderItemId);
                $sourceItemSelections[] = $sourceItemSelection;
                $this->deductSourceItemQty($sku, $sourceCode, $qtyToDeduct);
                $qtyToDeliver -= $qtyToDeduct;
            }
            if (!$this->isZero($qtyToDeliver)) {
                $isShippable = false;
            }
        }
        return $this->sourceSelectionResultFactory->create([
            'sourceItemSelections' => $sourceItemSelections,
            'isShippable' => $isShippable,
        ]);
    }

    /**
     * Check if is zero
     *
     * @param float $value
     * @return bool
     */
    private function isZero(float $value): bool
    {
        return $value < 0.0000001;
    }
    
    /**
     * Normalize SKU
     *
     * @param string $sku
     * @return string
     */
    private function normalizeSku(string $sku): string
    {
        return mb_convert_case($sku, MB_CASE_LOWER, 'UTF-8');
    }
    
    /**
     * Get order item
     * 
     * @param int $orderItemId
     * @return \Magento\Sales\Api\Data\OrderItemInterface|null
     */
    private function getOrderItem(int $orderItemId): ?\Magento\Sales\Api\Data\OrderItemInterface
    {
        if (empty($orderItemId)) {
            return null;
        }
        try {
            return $this->orderItemRepository->get($orderItemId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
            return null;
        }
    }
    
    /**
     * Get source item
     *
     * @param string $sku
     * @param string $sourceCode
     * @return \Magento\InventoryApi\Api\Data\SourceItemInterface|null
     */
    private function getSourceItem(string $sku, string $sourceCode): ?\Magento\InventoryApi\Api\Data\SourceItemInterface
    {
        if (array_key_exists($sku, $this->sourceItems) && array_key_exists($sourceCode, $this->sourceItems[$sku])) {
            return $this->sourceItems[$sku][$sourceCode];
        }
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(\Magento\InventoryApi\Api\Data\SourceItemInterface::SKU, $sku)
            ->addFilter(\Magento\InventoryApi\Api\Data\SourceItemInterface::SOURCE_CODE, $sourceCode)
            ->create();
        $sourceItemsResult = $this->sourceItemRepository->getList($searchCriteria);
        $this->sourceItems[$sku][$sourceCode] = $sourceItemsResult->getTotalCount() > 0 ? current($sourceItemsResult->getItems()) : null;
        return $this->sourceItems[$sku][$sourceCode];
    }
    
    /**
     * Get in stock source item
     * 
     * @param string $sku
     * @param string $sourceCode
     * @return \Magento\InventoryApi\Api\Data\SourceItemInterface|null
     */
    private function getInStockSourceItem(string $sku, string $sourceCode): ?\Magento\InventoryApi\Api\Data\SourceItemInterface
    {
        $sourceItem = $this->getSourceItem($sku, $sourceCode);
        if (
            $sourceItem === null || 
            $sourceItem->getStatus() !== \Magento\InventoryApi\Api\Data\SourceItemInterface::STATUS_IN_STOCK
        ) {
            return null;
        }
        return $sourceItem;
    }
    
    /**
     * Get source item qty available
     * 
     * @param string $sku
     * @param string $sourceCode
     * @return float
     */
    private function getSourceItemQtyAvailable(string $sku, string $sourceCode): float
    {
        $sourceItem = $this->getSourceItem($sku, $sourceCode);
        if (!$sourceItem) {
            return 0;
        }
        if (version_compare($this->productMetadata->getVersion(), '2.3.2', '>=')) {
            return (float) $this->getSourceItemQtyAvailable->execute($sourceItem);
        } else {
            return (float) $sourceItem->getQuantity();
        }
    }
    
    /**
     * Get source item quantity
     * 
     * @return float
     */
    private function getSourceItemQty(string $sku, string $sourceCode): float
    {
        if (array_key_exists($sku, $this->sourceItemQuantities) && array_key_exists($sourceCode, $this->sourceItemQuantities[$sku])) {
            return $this->sourceItemQuantities[$sku][$sourceCode];
        }
        return $this->sourceItemQuantities[$sku][$sourceCode] = $this->getSourceItemQtyAvailable($sku, $sourceCode);
    }
    
    /**
     * Deduct source item quantity
     * 
     * @param string $sku
     * @param string $sourceCode
     * @param float $quantityToDeduct
     * @return self
     */
    private function deductSourceItemQty(string $sku, string $sourceCode, float $quantityToDeduct): self
    {
        $this->sourceItemQuantities[$sku][$sourceCode] = $this->getSourceItemQty($sku, $sourceCode) - $quantityToDeduct;
        return $this;
    }
}