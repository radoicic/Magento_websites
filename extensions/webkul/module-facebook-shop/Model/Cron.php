<?php
/**
 * @category   Webkul
 * @package    Webkul_FacebookShop
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\FacebookShop\Model;

use Magento\Framework\App\Action\Context;

/**
 * custom cron actions
 */
class Cron
{

    /**
     * @var \Webkul\FacebookShop\Helper\Data
     */
    protected $fbShopHelper;
    
    /**
     * @var Magento\Framework\App\Action\Context
     */
    protected $contextController;

   /**
    * @param Context $context
    * @param \Webkul\FacebookShop\Helper\Data $fbShopHelper
    */
    public function __construct(
        Context $context,
        \Webkul\FacebookShop\Helper\Data $fbShopHelper
    ) {
        $this->fbShopHelper = $fbShopHelper;
    }

    /**
     * synchronize catalog
     *
     * @return void
     */
    public function catalogSync()
    {
        $initiatedBy = 'Cron';
        $allowCron = $this->fbShopHelper->getConfigValue(
            'facebook_shop_configuration',
            'allow_periodic_csv_generation'
        );
        if ($allowCron) {
            $this->fbShopHelper->generateFbFeedCsv($initiatedBy) ;
        }
    }
}
