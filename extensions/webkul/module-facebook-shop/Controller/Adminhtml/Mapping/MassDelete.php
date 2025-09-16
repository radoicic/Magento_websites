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
namespace Webkul\FacebookShop\Controller\Adminhtml\Mapping;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Webkul\FacebookShop\Model\ResourceModel\Mapping\CollectionFactory;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * checks allowed role
     *
     * @return void
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_FacebookShop::mapping');
    }

    /**
     * execute
     *
     * @return void
     */
    public function execute()
    {
        $mappingIds = [];
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        foreach ($collection as $mapping) {
                $mappingIds[] = $mapping->getId();
                $this->removeItem($mapping);
        }
        $this->messageManager->addSuccess(__('Mapping(s) deleted succesfully'));
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Remove Item
     *
     * @param object $item
     */
    protected function removeItem($item)
    {
        $item->delete();
    }
}
