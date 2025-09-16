<?php

namespace Meetanshi\Callforprice\Controller\Adminhtml\Callforprice;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Ui\Component\MassAction\Filter;
use Meetanshi\Callforprice\Model\Callforprice;
use Meetanshi\Callforprice\Model\ResourceModel\Callforprice\CollectionFactory;

/**
 * Class MassDelete
 */
class MassDelete extends Action
{
    /**
     * @var Callforprice
     */
    protected $callforprice;
    /**
     * @var ManagerInterface
     */
    protected $messageManager;
    /**
     * @var Filter
     */
    protected $filter;
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * MassDelete constructor.
     * @param Callforprice $callforprice
     * @param Action\Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Callforprice $callforprice,
        Action\Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->callforprice = $callforprice;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $collectionSize = $collection->getSize();

            foreach ($collection as $item) {
                $id = $item['id'];
                $row = $this->callforprice->load($id);
                $row->delete();
            }
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $collectionSize));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        $this->_redirect('*/*/index');
    }
}
