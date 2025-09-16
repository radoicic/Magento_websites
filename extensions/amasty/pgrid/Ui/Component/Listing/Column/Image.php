<?php
declare(strict_types=1);

namespace Amasty\Pgrid\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class Image extends \Magento\Ui\Component\Listing\Columns\Column
{
    public const ALT_FIELD = 'name';

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    private $imageHelper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->imageHelper = $imageHelper;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Prepare Data Source
     * @see \Magento\Catalog\Ui\Component\Listing\Columns\Thumbnail
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');

            foreach ($dataSource['data']['items'] as &$item) {
                $product = new \Magento\Framework\DataObject($item);
                $imageFile = $product->getData($fieldName) ?: 'no_selection';
                $imageHelper = $this->imageHelper->init($product, 'product_listing_thumbnail')
                    ->setImageFile($imageFile);
                $item[$fieldName . '_src'] = $imageHelper->getUrl();
                $item[$fieldName . '_alt'] = $this->getAlt($item) ?: $imageHelper->getLabel();
                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    'catalog/product/edit',
                    ['id' => $product->getEntityId(), 'store' => $this->context->getRequestParam('store')]
                );
                $item[$fieldName . '_orig_src'] = $this->imageHelper
                    ->init($product, 'product_listing_thumbnail_preview')
                    ->setImageFile($imageFile)
                    ->getUrl();
            }
        }

        return $dataSource;
    }

    protected function getAlt(array $row): ?string
    {
        $altField = $this->getData('config/altField') ?: self::ALT_FIELD;

        return $row[$altField] ?? null;
    }
}
