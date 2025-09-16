<?php

namespace Meetanshi\Callforprice\Ui\Component\Listing\Column;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Catalog\Model\ProductRepository;

/**
 * Class ProductName
 */
class ProductName extends Column
{
    private $productRepository;

    /**
     * ProductName constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ProductRepository $productRepository
     * @param array $components
     * @param array $data
     * @param string $storeKey
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ProductRepository $productRepository,
        array $components = [],
        array $data = [],
        $storeKey = 'store_view'
    ) {
        $this->productRepository = $productRepository;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     * @throws NoSuchEntityException
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $productId = $item['productid'];
                if (!$productId) {
                    $item['productid'] = __('');
                } else {
                    $product = $this->productRepository->getById($productId);
                    $item['productid'] = __($product->getName() . '<br>');
                }
            }
        }
        return $dataSource;
    }
}
