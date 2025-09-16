<?php
 /**
  * Webkul Software
  *
  * @category Webkul
  * @package Webkul_FacebookShop
  * @author Webkul
  * @copyright Copyright (c)Webkul Software Private Limited (https://webkul.com)
  * @license https://store.webkul.com/license.html
  */
namespace Webkul\FacebookShop\Model\Source;

use \Magento\Framework\App\Config\ScopeConfigInterface;
 
class ConditionTypes extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
    }
  
    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $data = [
            ['value' => 'new', 'label' => __('New')],
            ['value' => 'refurbished', 'label' => __('Refurbished')],
            ['value' => 'used', 'label' => __('Used')],
            ['value' => 'used_fair', 'label' => __('Used Fair')],
            ['value' => 'used_good', 'label' => __('Used Good')],
            ['value' => 'used_like_new', 'label' => __('Used Like New')]
        ];
        return $data;
    }
}
