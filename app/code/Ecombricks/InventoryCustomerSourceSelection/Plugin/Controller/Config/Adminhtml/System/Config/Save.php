<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Controller\Config\Adminhtml\System\Config;

/**
 * Save configuration controller plugin
 */
class Save extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * Around execute
     * 
     * @param \Magento\Config\Controller\Adminhtml\System\Config\Save $subject
     * @param \Closure $proceed
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function aroundExecute(
        \Magento\Config\Controller\Adminhtml\System\Config\Save $subject,
        \Closure $proceed
    )
    {
        $this->setSubject($subject);
        $request = $subject->getRequest();
        $messageManager = $this->getSubjectPropertyValue('messageManager');
        try {
            $this->invokeSubjectMethod('_saveSection');
            $sectionCode = $request->getParam('section');
            $websiteCode = $request->getParam('website');
            $storeCode = $request->getParam('store');
            $configData = $this->invokeSubjectMethod('filterNodes', [
                'section' => $sectionCode,
                'website' => $websiteCode,
                'store' => $storeCode,
                'groups' => $this->invokeSubjectMethod('_getGroupsForSave'),
            ]);
            $config = $this->getSubjectPropertyValue('_configFactory')->create(['data' => $configData]);
            $config->save();
            $this->getSubjectPropertyValue('_eventManager')->dispatch(
                'admin_system_config_save',
                [
                    'configData' => $configData,
                    'request' => $request,
                ]
            );
            $messageManager->addSuccess(__('You saved the configuration.'));
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
            foreach (explode("\n", $exception->getMessage()) as $message) {
                $messageManager->addError($message);
            }
        } catch (\Exception $exception) {
            $messageManager->addException(
                $exception,
                __('Something went wrong while saving this configuration:').' '.$exception->getMessage()
            );
        }
        $this->invokeSubjectMethod('_saveState', $request->getPost('config_state'));
        $resultRedirect = $this->getSubjectPropertyValue('resultRedirectFactory')->create();
        return $resultRedirect->setPath(
            'adminhtml/system_config/edit',
            [
                '_current' => [
                    'section',
                    'website',
                    'store',
                    'source',
                ],
                '_nosid' => true
            ]
        );
    }
}