<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Product Labels for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Label\Ui\DataProvider\Label\Modifiers\Form;

use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Model\LabelRegistry;
use Amasty\Label\Model\ResourceModel\Label\Grid\Collection as CatalogPartsCollection;
use Amasty\Label\Model\ResourceModel\Label\Grid\CollectionFactory;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class AddCatalogPartsData implements ModifierInterface
{
    use LabelSpecificSettingsTrait;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var LabelRegistry
     */
    private $labelRegistry;

    public function __construct(
        CollectionFactory $collectionFactory,
        LabelRegistry $labelRegistry
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->labelRegistry = $labelRegistry;
    }

    protected function executeIfLabelExists(int $labelId, array $data): array
    {
        $collection = $this->getCollection($labelId);
        $labelData = $data[$labelId] ?? [];
        $catalogPartsData = $collection->getFirstItem()->getData();
        $labelData = array_merge($labelData, $catalogPartsData);
        $data[$labelId] = $labelData;

        return $data;
    }

    private function getCollection(int $labelId): CatalogPartsCollection
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(LabelInterface::LABEL_ID, $labelId);
        $collection->setPageSize(1);
        $collection->loadOnlyJoinedParts();

        return $collection;
    }
}
