<?php
declare(strict_types=1);

namespace TiDesign\ProductGridFix\Ui\DataProvider\Product\Listing\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ObjectManager;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\InventoryCatalogApi\Model\IsSingleSourceModeInterface;
use Magento\InventoryConfigurationApi\Model\GetAllowedProductTypesForSourceItemManagementInterface;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Listing\Columns\Column;

use TiDesign\ProductGridFix\Helper\Data;
use Magento\Store\Model\ResourceModel\Website\CollectionFactory as WebsiteCollectionFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\ImageFactory;
/**
 * Quantity Per Source modifier on CatalogInventory Product Grid
 */
class QuantityPerSource extends AbstractModifier
{
    /**
     * @var IsSingleSourceModeInterface
     */
    private $isSingleSourceMode;

    /**
     * @var SourceRepositoryInterface
     */
    private $sourceRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SourceItemRepositoryInterface
     */
    private $sourceItemRepository;

    /**
     * @var GetAllowedProductTypesForSourceItemManagementInterface
     */
    private $getAllowedProductTypesForSourceItemManagement;


	private $helper;
	private $websiteCollectionFactory;
	private $_productRepository;
	private $imageHelperFactory;
	
    /**
     * @param IsSingleSourceModeInterface $isSingleSourceMode
     * @param null $isSourceItemManagementAllowedForProductType @deprecated
     * @param SourceRepositoryInterface $sourceRepository
     * @param null $getSourceItemsBySku @deprecated
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SourceItemRepositoryInterface $sourceItemRepository
     * @param GetAllowedProductTypesForSourceItemManagementInterface $getAllowedProductTypesForSourceItemManagement
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
		Data $dataHelper,
		WebsiteCollectionFactory $websiteCollectionFactory,
		ProductRepositoryInterface $productRepository,
		ImageFactory $imageHelperFactory,
        IsSingleSourceModeInterface $isSingleSourceMode,
        $isSourceItemManagementAllowedForProductType,
        SourceRepositoryInterface $sourceRepository,
        $getSourceItemsBySku,
        SearchCriteriaBuilder $searchCriteriaBuilder = null,
        SourceItemRepositoryInterface $sourceItemRepository = null,
        GetAllowedProductTypesForSourceItemManagementInterface $getAllowedProductTypesForSourceItemManagement = null
    ) {
		$this->helper 					= $dataHelper;
		$this->websiteCollectionFactory = $websiteCollectionFactory;
		$this->_productRepository		= $productRepository;
		$this->imageHelperFactory 		= $imageHelperFactory;
        $objectManager 					= ObjectManager::getInstance();
        $this->isSingleSourceMode 		= $isSingleSourceMode;
        $this->sourceRepository 		= $sourceRepository;
        $this->searchCriteriaBuilder 	= $searchCriteriaBuilder ?: $objectManager->get(SearchCriteriaBuilder::class);
        $this->sourceItemRepository 	= $sourceItemRepository ?:
            $objectManager->get(SourceItemRepositoryInterface::class);
        $this->getAllowedProductTypesForSourceItemManagement = $getAllowedProductTypesForSourceItemManagement ?:
            $objectManager->get(GetAllowedProductTypesForSourceItemManagementInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function modifyData(array $data)
    {
        if (0 === $data['totalRecords'] || true === $this->isSingleSourceMode->execute()) {
            return $data;
        }

        $data['items'] = $this->getSourceItemsData($data['items']);

        return $data;
    }

    /**
     * Add qty per source to the items.
     *
     * @param array $dataItems
     * @return array
     */
    private function getSourceItemsData(array $dataItems): array
    {
        $itemsBySkus = [];
        $allowedProductTypes = $this->getAllowedProductTypesForSourceItemManagement->execute();

        foreach ($dataItems as $key => $item) {
            if (in_array($item['type_id'], $allowedProductTypes)) {
                $itemsBySkus[$item['sku']] = $key;
                continue;
            }
            $dataItems[$key]['quantity_per_source'] = [];
        }

        unset($item);

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(SourceItemInterface::SKU, array_keys($itemsBySkus), 'in')
            ->create();

        $sourceItems = $this->sourceItemRepository->getList($searchCriteria)->getItems();
        $sourcesBySourceCode = $this->getSourcesBySourceItems($sourceItems);

        foreach ($sourceItems as $sourceItem) {
            $sku = $sourceItem->getSku();
			
            if (isset($itemsBySkus[$sku])) {
                $source 		= $sourcesBySourceCode[$sourceItem->getSourceCode()];
				$website		= $this->helper->getData($source->getSourceCode());
				$websiteId		= $website['id'];
				$websiteName	= $website['name'];
				
//				echo $sku."-".$source->getSourceCode()."-".$websiteId."-".$websiteName."<br>";
				
                $qty = (float)$sourceItem->getQuantity();
					
					if($source->getSourceCode() == 'nzboxer' && (array_key_exists('quantity_per_source', $dataItems[$itemsBySkus[$sku]]))){
						$newdata = [
							'source_name' => $source->getName(),
							'source_code' => $source->getSourceCode(),
							'qty' => $qty,
							'website_id' => $websiteId,
							'website_name' => $websiteName,
						];
						if (array_key_exists('quantity_per_source', $dataItems[$itemsBySkus[$sku]])) {
							if(is_array($dataItems[$itemsBySkus[$sku]]['quantity_per_source'])){
								array_unshift($dataItems[$itemsBySkus[$sku]]['quantity_per_source'],$newdata);
							}
						}else{
							$dataItems[$itemsBySkus[$sku]]['quantity_per_source']=$newdata;
						}

					}else{	
						$dataItems[$itemsBySkus[$sku]]['quantity_per_source'][] = [
							'source_name' => $source->getName(),
							'source_code' => $source->getSourceCode(),
							'qty' => $qty,
							'website_id' => $websiteId,
							'website_name' => $websiteName,
						];
					}					
				
            if (array_key_exists('quantity_per_source', $dataItems[$itemsBySkus[$sku]])) {
				usort( $dataItems[$itemsBySkus[$sku]]['quantity_per_source'], fn($a, $b) => $b['qty'] <=> $a['qty']);
			
		
				$dataItems[$itemsBySkus[$sku]]['tidesign_product_data'] = $dataItems[$itemsBySkus[$sku]]['quantity_per_source'];
			}
			$websites = [];
			$collection = $this->websiteCollectionFactory->create();
			foreach($collection as $website) {
				$websites[$website->getWebsiteId()] = $website->getName();
			}
			$dataItems[$itemsBySkus[$sku]]['tidesign_website_data'] = $websites;


			$c_product = $this->_productRepository->get($sku);
			//$th = $c_product->getData('thumbnail');
			$th = $this->imageHelperFactory->create()->init($c_product, 'product_thumbnail_image')->getUrl();
			$p_name = $c_product->getName();
			$dataItems[$itemsBySkus[$sku]]['tidesign_product_image'] = $th;
			$dataItems[$itemsBySkus[$sku]]['tidesign_product_name'] = $p_name;
			}

        }


        return $dataItems;
    }

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta)
    {
        if (true === $this->isSingleSourceMode->execute()) {
            return $meta;
        }

        $meta = array_replace_recursive(
            $meta,
            [
                'product_columns' => [
                    'children' => [
                        'quantity_per_source' => $this->getQuantityPerSourceMeta(),
                        'qty' => [
                            'arguments' => null,
                        ],
                    ],
                ],
            ]
        );
        return $meta;
    }

    /**
     * Qty per source metadata for rendering.
     *
     * @return array
     */
    private function getQuantityPerSourceMeta(): array
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'sortOrder' => 76,
                        'filter' => false,
                        'sortable' => false,
                        'label' => __('Source Qty'),
                        'dataType' => Text::NAME,
                        'componentType' => Column::NAME,
                        'component' => 'TiDesign_ProductGridFix/js/quantity-per-source',
						'width' => '200px',
						'resizeEnabled' => true,
						'resizeDefaultWidth' => 200,
                    ]
                ],
            ],
        ];
    }

    /**
     * Get all sources by source items codes.
     *
     * @param SourceItemInterface[] $sourceItems
     * @return array
     */
    private function getSourcesBySourceItems(array $sourceItems): array
    {
        $newSourceCodes = $sourcesBySourceCodes = [];

        foreach ($sourceItems as $sourceItem) {
            $newSourceCodes[$sourceItem->getSourceCode()] = $sourceItem->getSourceCode();
        }

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(SourceInterface::SOURCE_CODE, array_keys($newSourceCodes), 'in')
            ->create();
        $sources = $this->sourceRepository->getList($searchCriteria)->getItems();

        foreach ($sources as $source) {
            $sourcesBySourceCodes[$source->getSourceCode()] = $source;
        }

        return $sourcesBySourceCodes;
    }
}
