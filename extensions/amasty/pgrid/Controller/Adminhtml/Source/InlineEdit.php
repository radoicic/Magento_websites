<?php
declare(strict_types=1);

namespace Amasty\Pgrid\Controller\Adminhtml\Source;

use Amasty\Pgrid\Model\Inventory\ObjectFactory;
use Magento\Backend\App\Action;
use Magento\Catalog\Model\Indexer\Product\Full;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\DB\Select;
use Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;

class InlineEdit extends Action
{
    public const ADMIN_RESOURCE = 'Magento_InventoryApi::source';

    /**
     * @var ObjectFactory
     */
    private $msiObjectFactory;

    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var Full
     */
    private $indexer;

    public function __construct(
        Action\Context $context,
        ObjectFactory $msiObjectFactory,
        CollectionFactory $productCollectionFactory,
        Full $indexer
    ) {
        parent::__construct($context);
        $this->msiObjectFactory = $msiObjectFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->indexer = $indexer;
    }

    public function execute()
    {
        /** @var SourceItemInterfaceFactory $sourceItemModelFactory */
        $sourceItemModelFactory = $this->msiObjectFactory->createMsiObject(
            SourceItemInterfaceFactory::class
        );

        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!$this->getRequest()->getParam('isAjax') || empty($postItems)) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        $sourceItems = [];
        if (!empty($postItems) && is_array($postItems)) {
            $mapping = [];
            $productCollection = $this->productCollectionFactory->create();
            $productCollection->getSelect()
                ->reset(Select::COLUMNS)
                ->columns(['entity_id', 'sku']);
            if (isset(end($postItems)['product_id'])) {
                $productIds = [end($postItems)['product_id']];
            } else {
                $productIds = array_keys($postItems);
            }
            $productCollection->addFieldToFilter('entity_id', $productIds);

            foreach ($productCollection->getData() as $item) {
                $mapping[$item['entity_id']] = $item['sku'];
            }

            foreach ($postItems as $entityId => $data) {
                if (isset($data['product_id'])) {
                    $entityId = $data['product_id'];
                }
                if (!isset($data['source_code']) || !isset($mapping[$entityId])) {
                    continue;
                }
                $sourceItemModel = $sourceItemModelFactory->create();

                $sourceItemModel->setStatus(
                    isset($data['source_item_status'])
                        ? (int)$data['source_item_status']
                        : 1
                );
                $sourceItemModel->setSourceCode($data['source_code']);
                $sourceItemModel->setQuantity(isset($data['quantity']) ? (float)$data['quantity'] : 0);
                $sourceItemModel->setSku($mapping[$entityId]);

                $this->indexer->executeRow($entityId);

                $sourceItems[] = $sourceItemModel;
            }
        }

        if (!empty($sourceItems)) {
            /** @var SourceItemsSaveInterface $sourceItemsSaveObject */
            $sourceItemsSaveObject = $this->msiObjectFactory->createMsiObject(
                SourceItemsSaveInterface::class
            );
            if ($sourceItemsSaveObject) {
                $sourceItemsSaveObject->execute($sourceItems);
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
}
