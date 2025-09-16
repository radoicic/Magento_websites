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
namespace Webkul\FacebookShop\Model\Fb;

class Attributes implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var null|array
     */
    protected $options;

    /**
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $collectionFactory
     * @param \Webkul\FacebookShop\Helper\Data $fbShopHelper
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $collectionFactory,
        \Webkul\FacebookShop\Helper\Data $fbShopHelper
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->fbShopHelper = $fbShopHelper;
    }

    /**
     * return all fb attributes
     *
     * @return void
     */
    public function toOptionArray()
    {
        $availableOptions[] = ['value' => 0, 'label' => '--- Please Select ---'];
        $fbAttributes = $this->fbShopHelper->getAllFbAttributes();
        foreach ($fbAttributes as $attributeId => $attrlabel) {
             $availableOptions[] = ['value' => $attributeId, 'label' => $attrlabel];
        }
        return $availableOptions;
    }
}
