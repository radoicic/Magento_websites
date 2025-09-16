<?php
declare(strict_types=1);

namespace Amasty\Pgrid\Ui\DataProvider\Product\Listing;

use Amasty\Pgrid\Model\Inventory\ObjectFactory;
use Amasty\Pgrid\Model\ResourceModel\SourceItemResource;
use Magento\Framework\App\RequestInterface;
use Magento\Inventory\Model\ResourceModel\Source\Collection;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\Data\CollectionFactory;

class SourceDataProvider extends AbstractDataProvider
{
    /**
     * @var ObjectFactory
     */
    private $msiObjectFactory;

    /**
     * @var string
     */
    private $productSku;

    /**
     * @var string
     */
    private $productId;

    /**
     * @var string
     */
    private $productTypeId;

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var SourceItemResource
     */
    private $sourceItemResource;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        SourceItemResource $sourceItemResource,
        ObjectFactory $msiObjectFactory,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collectionFactory = $collectionFactory;
        $this->msiObjectFactory = $msiObjectFactory;
        $this->collection = $this->getSourceCollectionObject();
        $this->productSku = $request->getParam('sku');
        $this->productId = $request->getParam('entity_id');
        $this->productTypeId = $request->getParam('type_id');
        $this->sourceItemResource = $sourceItemResource;
    }

    private function getSourceCollectionObject()
    {
        return $this->msiObjectFactory->createMsiObject(Collection::class) ?? $this->collectionFactory->create();
    }

    public function getData(): array
    {
        if (!$this->productSku) {
            return parent::getData();
        }

        $data = parent::getData();

        /** @var SourceItemInterface $item */
        foreach ($data['items'] as &$item) {
            if (!isset($item['source_code'])) {
                continue;
            }
            $item['sku'] = $this->productSku;
            $item['product_id'] = $this->productId;
            $item['type_id'] = $this->productTypeId;

            $itemData = $this->sourceItemResource->getSourceItemData($this->productSku, $item['source_code']);
            $item['quantity'] = isset($itemData['quantity']) ? (float)$itemData['quantity'] : 0;
            $item['source_item_status'] =
                !isset($itemData['status']) ? SourceItemInterface::STATUS_IN_STOCK : $itemData['status'];
        }

        $data['totalRecords'] += 1; // temp solution for magento issue https://github.com/magento/magento2/issues/25457

        return $data;
    }
}
