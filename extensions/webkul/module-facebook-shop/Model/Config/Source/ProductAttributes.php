<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_FacebookShop
 * @author    Webkul
 * @copyright Copyright (c)Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\FacebookShop\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ProductAttributes implements OptionSourceInterface
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    protected $_attributeFactory;

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeFactory
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeFactory
    ) {
        $this->_attributeFactory = $attributeFactory;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions[] = ['value' => 0, 'label' => '--- Please Select ---'];
        $attributeInfo = $this->_attributeFactory->create()->addVisibleFilter();

        foreach ($attributeInfo as $attributes) {
            $attributeId        = $attributes->getAttributeId();
            $attrlabel          = $attributes->getFrontendLabel();
            $availableOptions[] = ['value' => $attributeId, 'label' => $attrlabel];
        }
        return $availableOptions;
    }
}
