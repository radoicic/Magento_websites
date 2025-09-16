<?php

namespace TiDesign\Videobackground\Controller\Adminhtml\Videolist;

use Magento\Backend\App\Action;
use TiDesign\Videobackground\Model\Videolist;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \TiDesign\Videobackground\Model\VideolistFactory
     */
    private $videolistFactory;

    /**
     * @var \TiDesign\Videobackground\Api\VideolistRepositoryInterface
     */
    private $videolistRepository;

    /**
     * @param Action\Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param \TiDesign\Videobackground\Model\VideolistFactory $videolistFactory
     * @param \TiDesign\Videobackground\Api\VideolistRepositoryInterface $videolistRepository
     */
    public function __construct(
        Action\Context $context,
        DataPersistorInterface $dataPersistor,
        \TiDesign\Videobackground\Model\VideolistFactory $videolistFactory = null,
        \TiDesign\Videobackground\Api\VideolistRepositoryInterface $videolistRepository = null
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->videolistFactory = $videolistFactory
            ?: \Magento\Framework\App\ObjectManager::getInstance()->get(\TiDesign\Videobackground\Model\VideolistFactory::class);
        $this->videolistRepository = $videolistRepository
            ?: \Magento\Framework\App\ObjectManager::getInstance()->get(\TiDesign\Videobackground\Api\VideolistRepositoryInterface::class);
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
//echo"<pre>";print_r($data);die();		
		$data['store_id'] 		= implode(',',$data['store_id']);
		if(array_key_exists ('category_id', $data)){
			if( is_array($data['category_id']) && !empty(array_filter($data['category_id'])) ){
				$data['category_id'] 	= implode(',',$data['category_id']);
			}else{
				 $data['category_id'] = null;
			}
		}else{
			$data['category_id'] = null;
		}
		(is_array($data['static_pages'])) ? $data['static_pages'] 	= implode(',',$data['static_pages']) : $data['static_pages'] = null;
		(is_array($data['page_id'])) ? $data['page_id'] 	= implode(',',$data['page_id']) : $data['page_id'] = null;
		
		
        if (isset($data['local_webm'][0]['name'])) {
            $data['local_webm'] = $data['local_webm'][0]['name'];
        } else {
            $data['local_webm'] = null;
        }		
		
        if (isset($data['local_mp4'][0]['name'])) {
            $data['local_mp4'] = $data['local_mp4'][0]['name'];
        } else {
            $data['local_mp4'] = null;
        }		
		
			
		
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {

            if (empty($data['id'])) {
                $data['id'] = null;
            }

            /** @var \TiDesign\Videobackground\Model\Videolist $model */
            $model = $this->videolistFactory->create();

            $id = $this->getRequest()->getParam('id');
            if ($id) {
                try {
                    $model = $this->videolistRepository->getById($id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This record no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }

            $model->setData($data);

            $this->_eventManager->dispatch(
                'videobackground_videolist_prepare_save',
                ['videolist' => $model, 'request' => $this->getRequest()]
            );

            try {
                $this->videolistRepository->save($model);
                $this->messageManager->addSuccessMessage(__('Save successfull...'));
                $this->dataPersistor->clear('videobackground_videolist');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e->getPrevious() ?:$e);
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the news.'));
            }

            $this->dataPersistor->set('videobackground_videolist', $data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
?>
