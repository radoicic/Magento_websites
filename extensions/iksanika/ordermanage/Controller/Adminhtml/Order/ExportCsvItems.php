<?php
/**
 * Iksanika llc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.iksanika.com/products/IKS-LICENSE.txt
 *
 * @category   Iksanika
 * @package    Iksanika_Ordermanage
 * @copyright  Copyright (c) 2015 Iksanika llc. (http://www.iksanika.com)
 * @license    http://www.iksanika.com/products/IKS-LICENSE.txt
 */
namespace Iksanika\Ordermanage\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Sales\Controller\Adminhtml\Order;
use Magento\Framework\Controller\ResultFactory;

class ExportCsvItems extends \Magento\Sales\Controller\Adminhtml\Order
{
    public static $exportFileName = 'ordersItems';

    /**
     * Export product(s) in CSV format action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $content    = $this->_view->getLayout()->addBlock('Iksanika\Ordermanage\Block\Adminhtml\Order\Grid')->getCsv(\Iksanika\Ordermanage\Model\Export::ORDERED_ITEMS);
        $this->_sendUploadResponse(self::$exportFileName.'.csv', $content);
    }
    
    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
//        $response->setHeader('HTTP/1.1 200 OK','');
        
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);

        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        //die;
    }
    
    
    
}
