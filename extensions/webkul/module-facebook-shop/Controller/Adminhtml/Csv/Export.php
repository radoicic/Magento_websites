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
namespace Webkul\FacebookShop\Controller\Adminhtml\Csv;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Webkul\FacebookShop\Helper\Data as FacebookShopHelper;

class Export extends Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var FacebookShopHelper $fbShopHelper
     */
    protected $fbShopHelper;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param FacebookShopHelper $fbShopHelper
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        FacebookShopHelper $fbShopHelper
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->fbShopHelper = $fbShopHelper;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_FacebookShop::config_facebookshop');
    }

   /**
    * generate fb feed csv manually
    *
    * @return void
    */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        try {
            $data = $this->getRequest()->getParams();
            $iniatiatedBy = $data['initiatedBy'];
            $result = $this->fbShopHelper->generateFbFeedCsv($iniatiatedBy);
            if (!empty($result['error'])) {
                return $resultJson->setData(['msg' => 'Generation failed due to some error. Please check Csv logs']);
            }
            if (!empty($result['warning'])) {
                return $resultJson->setData(['msg' => $result['message']]);
            }
            return $resultJson->setData(['msg' => 'Feed Csv Generated Successfully']);
        } catch (\Exception $e) {
            $result['msg'] = $e->getMessage();
            return $resultJson->setData($result);
        }
    }
}
