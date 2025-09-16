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
 
class Gender extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
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
            ['value' => 'Female', 'label' => __('Female')],
            ['value' => 'Male', 'label' => __('Male')],
            ['value' => 'Unisex', 'label' => __('Unisex')]
        ];
        return $data;
    }
}
