<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_FacebookShop
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\FacebookShop\Model\Config\Source;

/**
 * Used in creating options for getting product type value.
 */
class Frequency
{
    /**
     * @var \Magento\Catalog\Model\ProductTypes\ConfigInterface
     */
    protected $_config;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $manager;
    
    /**
     * @param \Magento\Framework\Module\Manager $manager
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $config
     */
    public function __construct(
        \Magento\Framework\Module\Manager $manager,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $config
    ) {
        $this->manager = $manager;
        $this->_config = $config;
    }
    
    /**
     * Options getter.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data = [
            ['value' => '0', 'label' => __('Weekly')],
            ['value' => '1', 'label' => __('Monthly')],
            ['value' => '2', 'label' => __('Daily')]
        ];
        return $data;
    }
}
