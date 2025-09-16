<?php
declare(strict_types=1);

namespace Amasty\Pgrid\Ui\Component\Listing\Column;

use Amasty\Pgrid\Model\Inventory\ObjectFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface;
use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;
use Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class SourceQuantity extends Column
{
    /**
     * @var ObjectFactory
     */
    private $msiObjectFactory;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ObjectFactory $msiObjectFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->msiObjectFactory = $msiObjectFactory;
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item['valid'] = false;

                if (isset($item['sku'], $item['type_id'])) {
                    $item['valid'] = $this->isItemValid($item['sku'], $item['type_id']);
                }
            }
        }

        return $dataSource;
    }

    private function isItemValid($sku, $typeId): bool
    {
        /** @var IsSourceItemManagementAllowedForProductTypeInterface $allowedForProductObject */
        $allowedForProductObject = $this->msiObjectFactory->createMsiObject(
            IsSourceItemManagementAllowedForProductTypeInterface::class
        );

        /** @var GetStockItemConfigurationInterface $stockItemConfigurationObject */
        $stockItemConfigurationObject = $this->msiObjectFactory->createMsiObject(
            GetStockItemConfigurationInterface::class
        );

        /** @var DefaultStockProviderInterface $defaultStockProviderObject */
        $defaultStockProviderObject = $this->msiObjectFactory->createMsiObject(
            DefaultStockProviderInterface::class
        );

        if (!$allowedForProductObject && !$stockItemConfigurationObject && !$defaultStockProviderObject) {
            return false;
        }

        $isValid = false;
        if ($allowedForProductObject->execute($typeId) === true) {
            $isValid = $stockItemConfigurationObject->execute($sku, $defaultStockProviderObject->getId())
                ->isManageStock();
        }

        return $isValid;
    }
}
