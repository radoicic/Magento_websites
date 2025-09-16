<?php

namespace TiDesign\Domainmanager\Controller\Adminhtml\Domainlist;

use Magento\Backend\App\Action;
use TiDesign\Domainmanager\Model\Domainlist;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \TiDesign\Domainmanager\Model\DomainlistFactory
     */
    private $domainlistFactory;

    /**
     * @var \TiDesign\Domainmanager\Api\DomainlistRepositoryInterface
     */
    private $domainlistRepository;

    /**
     * @param Action\Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param \TiDesign\Domainmanager\Model\DomainlistFactory $domainlistFactory
     * @param \TiDesign\Domainmanager\Api\DomainlistRepositoryInterface $domainlistRepository
     */
    public function __construct(
        Action\Context $context,
        DataPersistorInterface $dataPersistor,
        \TiDesign\Domainmanager\Model\DomainlistFactory $domainlistFactory = null,
        \TiDesign\Domainmanager\Api\DomainlistRepositoryInterface $domainlistRepository = null
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->domainlistFactory = $domainlistFactory
            ?: \Magento\Framework\App\ObjectManager::getInstance()->get(\TiDesign\Domainmanager\Model\DomainlistFactory::class);
        $this->domainlistRepository = $domainlistRepository
            ?: \Magento\Framework\App\ObjectManager::getInstance()->get(\TiDesign\Domainmanager\Api\DomainlistRepositoryInterface::class);
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();	
		if(array_key_exists ('dynamic_rows_container', $data)){
			if( is_array($data['dynamic_rows_container']) && !empty(array_filter($data['dynamic_rows_container'])) ){
				$data['td_url'] 	= implode(",", array_column($data['dynamic_rows_container'],"td_url"));
			}else{
				 $data['td_url'] = null;
			}
		}else{
			$data['td_url'] = null;
		}			
		unset($data['dynamic_rows_container']);

		if(array_key_exists ('td_category', $data)){
			if( is_array($data['td_category']) && !empty(array_filter($data['td_category'])) ){
				$data['td_category'] 	= implode(',',$data['td_category']);
			}else{
				 $data['td_category'] = null;
			}
		}else{
			$data['td_category'] = null;
		}


				
		
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {

            if (empty($data['td_id'])) {
                $data['td_id'] = null;
            }

            /** @var \TiDesign\Domainmanager\Model\Domainlist $model */
            $model = $this->domainlistFactory->create();

            $id = $this->getRequest()->getParam('td_id');
            if ($id) {
                try {
                    $model = $this->domainlistRepository->getById($id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This record no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }

            $model->setData($data);

            $this->_eventManager->dispatch(
                'domainmanager_domainlist_prepare_save',
                ['domainlist' => $model, 'request' => $this->getRequest()]
            );

            try {
                $this->domainlistRepository->save($model);
                $this->messageManager->addSuccessMessage(__('Save successfull...'));
                $this->dataPersistor->clear('domainmanager_domainlist');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getTdId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e->getPrevious() ?:$e);
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the news.'));
            }

            $this->dataPersistor->set('domainmanager_domainlist', $data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('td_id')]);
        }
        return $resultRedirect->setPath('*/*/');
		
		
    }
}
?>
