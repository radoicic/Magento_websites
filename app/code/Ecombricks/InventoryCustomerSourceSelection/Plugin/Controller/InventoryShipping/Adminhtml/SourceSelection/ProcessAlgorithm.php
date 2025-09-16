<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Controller\InventoryShipping\Adminhtml\SourceSelection;

/**
 * Source selection process algorithm controller plugin
 */
class ProcessAlgorithm
{
    /**
     * Get inventory request from order
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySourceSelection\GetInventoryRequestFromOrder 
     */
    private $getInventoryRequestFromOrder;

    /**
     * Get source by source code
     * 
     * @var \Ecombricks\InventoryCommon\Model\GetSourceBySourceCode
     */
    private $getSourceBySourceCode;
    
    /**
     * Request
     * 
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;
    
    /**
     * Result factory
     * 
     * @var \Magento\Framework\Controller\ResultFactory
     */
    private $resultFactory;
    
    /**
     * Item request factory
     * 
     * @var \Magento\InventorySourceSelectionApi\Api\Data\ItemRequestInterfaceFactory
     */
    private $itemRequestFactory;

    /**
     * Get default source selection algorithm code
     * 
     * @var \Magento\InventorySourceSelectionApi\Api\GetDefaultSourceSelectionAlgorithmCodeInterface
     */
    private $getDefaultSourceSelectionAlgorithmCode;
    
    /**
     * Source selection service
     * 
     * @var \Magento\InventorySourceSelectionApi\Api\SourceSelectionServiceInterface
     */
    private $sourceSelectionService;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySourceSelection\GetInventoryRequestFromOrder $getInventoryRequestFromOrder
     * @param \Ecombricks\InventoryCommon\Model\GetSourceBySourceCode $getSourceBySourceCode
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param \Magento\InventorySourceSelectionApi\Api\Data\ItemRequestInterfaceFactory $itemRequestFactory
     * @param \Magento\InventorySourceSelectionApi\Api\GetDefaultSourceSelectionAlgorithmCodeInterface $getDefaultSourceSelectionAlgorithmCode
     * @param \Magento\InventorySourceSelectionApi\Api\SourceSelectionServiceInterface $sourceSelectionService
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCustomerSourceSelection\Model\InventorySourceSelection\GetInventoryRequestFromOrder $getInventoryRequestFromOrder,
        \Ecombricks\InventoryCommon\Model\GetSourceBySourceCode $getSourceBySourceCode,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\InventorySourceSelectionApi\Api\Data\ItemRequestInterfaceFactory $itemRequestFactory,
        \Magento\InventorySourceSelectionApi\Api\GetDefaultSourceSelectionAlgorithmCodeInterface $getDefaultSourceSelectionAlgorithmCode,
        \Magento\InventorySourceSelectionApi\Api\SourceSelectionServiceInterface $sourceSelectionService
    )
    {
        $this->getInventoryRequestFromOrder = $getInventoryRequestFromOrder;
        $this->getSourceBySourceCode = $getSourceBySourceCode;
        $this->request = $request;
        $this->resultFactory = $resultFactory;
        $this->itemRequestFactory = $itemRequestFactory;
        $this->getDefaultSourceSelectionAlgorithmCode = $getDefaultSourceSelectionAlgorithmCode;
        $this->sourceSelectionService = $sourceSelectionService;
    }

    /**
     * Around execute
     * 
     * @param \Magento\InventoryShippingAdminUi\Controller\Adminhtml\SourceSelection\ProcessAlgorithm $subject
     * @param \Closure $proceed
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function aroundExecute(
        \Magento\InventoryShippingAdminUi\Controller\Adminhtml\SourceSelection\ProcessAlgorithm $subject,
        \Closure $proceed
    ) : \Magento\Framework\Controller\ResultInterface
    {
        $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        $itemRequests = $this->getItemRequests();
        if (empty($itemRequests)) {
            return $resultJson;
        }
        $sourceSelectionResult = $this->sourceSelectionService->execute(
            $this->getInventoryRequestFromOrder->execute($this->getOrderId(), $itemRequests),
            $this->getAlgorithmCode()
        );
        $sourceNames = $result = [];
        foreach ($this->getRequestData() as $requestItem) {
            $orderItemId = (int) $requestItem['orderItem'];
            foreach ($sourceSelectionResult->getSourceSelectionItems() as $selectionItem) {
                if ($selectionItem->getExtensionAttributes()->getOrderItemId() !== $orderItemId) {
                    continue;
                }
                $sourceCode = (string) $selectionItem->getSourceCode();
                $source = $this->getSourceBySourceCode->execute($sourceCode);
                $sourceName = $source ? $source->getName() : $sourceCode;
                $sourceNames[$sourceCode] = $sourceName;
                $result[$orderItemId][] = [
                    'sourceName' => $sourceName,
                    'sourceCode' => $sourceCode,
                    'qtyAvailable' => $selectionItem->getQtyAvailable(),
                    'qtyToDeduct' => $selectionItem->getQtyToDeduct(),
                ];
                
            }
        }
        foreach ($sourceNames as $sourceCode => $sourceName) {
            $result['sourceCodes'][] = ['value' => (string) $sourceCode, 'label' => $sourceName];
        }
        $resultJson->setData($result);
        return $resultJson;
    }

    /**
     * Get POST data
     * 
     * @return array
     */
    private function getPostData(): array
    {
        return $this->request->getPost()->toArray();
    }
    
    /**
     * Get request data
     * 
     * @return array|null
     */
    private function getRequestData(): ?array
    {
        $data = $this->getPostData();
        return $data['requestData'] ?? null;
    }
    
    /**
     * Get order ID
     * 
     * @return int
     */
    private function getOrderId(): int
    {
        $data = $this->getPostData();
        return (int) $data['orderId'];
    }
    
    /**
     * Get algorithm code
     * 
     * @return string
     */
    private function getAlgorithmCode(): string
    {
        $data = $this->getPostData();
        return $data['algorithmCode'] ?? $this->getDefaultSourceSelectionAlgorithmCode->execute();
    }
    
    /**
     * Get item requests
     *
     * @return array
     */
    private function getItemRequests(): array
    {
        $itemRequests = [];
        $requestData = $this->getRequestData();
        if (empty($requestData)) {
            return $itemRequests;
        }
        foreach ($requestData as $requestItem) {
            $itemRequest = $this->itemRequestFactory->create([
                'sku' => $requestItem['sku'],
                'qty' => $requestItem['qty']
            ]);
            $itemRequest->getExtensionAttributes()->setOrderItemId((int) $requestItem['orderItem']);
            $itemRequests[] = $itemRequest;
        }
        return $itemRequests;
    }
}