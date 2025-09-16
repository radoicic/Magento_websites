<?php
namespace TiDesign\Consignment\Controller\Adminhtml\Deletesource;

class Delete extends \Magento\Backend\App\Action
{
	protected 	$_resource;
	
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\App\ResourceConnection $resource
	) {
		$this->_resource 	= $resource;
		parent::__construct($context);
	}
	public function execute()
    {
		$resultRedirect = $this->resultRedirectFactory->create();
		$postData = $this->getRequest()->getPostValue();
		if (!empty($postData)) {
			try {
				$sourceCode = $postData['td_source'];
				$connection = $this->_resource->getConnection();
				$tableName 	= $connection->getTableName('inventory_source');
				$whereConditions = [
					$connection->quoteInto('source_code = ?', $sourceCode),
				];
				$deleteRows = $connection->delete($tableName, $whereConditions);
			} catch (LocalizedException $e) {
				$this->messageManager->addErrorMessage(__('Error'));
				return $resultRedirect->setPath('*/*/');
			}
		}
		$this->messageManager->addSuccess(__('Delete successfull'));
		return $resultRedirect->setPath('*/*/');		
	}
}