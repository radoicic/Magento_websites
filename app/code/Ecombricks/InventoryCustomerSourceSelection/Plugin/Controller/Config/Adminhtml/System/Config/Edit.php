<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Controller\Config\Adminhtml\System\Config;

/**
 * Edit configuration controller plugin
 */
class Edit extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * Around execute
     * 
     * @param \Magento\Config\Controller\Adminhtml\System\Config\Edit $subject
     * @param \Closure $proceed
     * @return \Magento\Framework\App\ResponseInterface|void
     */
    public function aroundExecute(
        \Magento\Config\Controller\Adminhtml\System\Config\Edit $subject,
        \Closure $proceed
    )
    {
        $this->setSubject($subject);
        $request = $subject->getRequest();
        $sectionCode = $request->getParam('section');
        $websiteCode = $request->getParam('website');
        $storeCode = $request->getParam('store');
        $sourceCode = (string) $request->getParam('source');
        $section = $this->getSubjectPropertyValue('_configStructure')->getElement($sectionCode);
        if ($sectionCode && !$section->isVisible($websiteCode, $storeCode)) {
            $redirectResult = $this->getSubjectPropertyValue('resultRedirectFactory')->create();
            return $redirectResult->setPath(
                'adminhtml/*/',
                [
                    'website' => $websiteCode,
                    'store' => $storeCode,
                    'source' => $sourceCode,
                ]
            );
        }
        $resultPage = $this->getSubjectPropertyValue('resultPageFactory')->create();
        $resultPage->setActiveMenu('Magento_Config::system_config');
        $resultPage->getLayout()->getBlock('menu')->setAdditionalCacheKeyInfo([$sectionCode]);
        $resultPage->addBreadcrumb(__('System'), __('System'), $subject->getUrl('*\/system'));
        $resultPage->getConfig()->getTitle()->prepend(__('Configuration'));
        return $resultPage;
    }
}