<?php
namespace TiDesign\Consignment\Controller\Adminhtml\Consignmentlist;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use TiDesign\Consignment\Model\ResourceModel\Consignmentlist\CollectionFactory;

/**
 * Class MassDelete
 */
class Disableproducts extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
	
	protected $cacheManager;

	protected $request;
	protected $formKey;
	
	private 	$consignmentlistRepository;
    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
		Context $context, 
		Filter $filter, 
		CollectionFactory $collectionFactory,
		\Magento\Framework\App\Cache\Manager $cacheManager,
		\Magento\Framework\Data\Form\FormKey $formKey,
		\Magento\Framework\App\Request\Http $request,
		\TiDesign\Consignment\Api\ConsignmentlistRepositoryInterface $consignmentlistRepository = null
	){
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
		$this->cacheManager = $cacheManager;
		$this->consignmentlistRepository = $consignmentlistRepository
            ?: \Magento\Framework\App\ObjectManager::getInstance()->get(\TiDesign\Consignment\Api\ConsignmentlistRepositoryInterface::class);
		$this->request = $request;
		$this->formKey = $formKey;
		$this->request->setParam('form_key', $this->formKey->getFormKey());			
        parent::__construct($context);
    }
	
    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
		
		$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
		$postData = $this->getRequest()->getPostValue();
		$consignment 		= (int) $this->getRequest()->getParam('consignment', 0);
		
		if (!empty($postData)) {
			if($consignment){
				if(count($postData['disable_id'])){
					$data['td_id']	= $consignment;
					
					
					
					try {
						$model = $this->consignmentlistRepository->getById($consignment);
					} catch (LocalizedException $e) {
						$this->messageManager->addErrorMessage(__('This record no longer exists.'));
						return $resultRedirect->setPath('*/*/');
					}
				}
			}
			
		
	
			$dis = array_filter(array_merge(explode(',', $model->getTdDisabled()),$postData['disable_id']));
//			echo $consignment."-".$model->getTdDisabled();die();
			$data['td_disabled'] = implode(',' , $dis);
			$model->setData($data);
			try {
				$this->consignmentlistRepository->save($model);
				$this->messageManager->addSuccessMessage(__('Products disabled'));
				$this->cacheManager->flush($this->cacheManager->getAvailableTypes());
				//$this->cacheManager->flush(['collections']);
				
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e->getPrevious() ?:$e);
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the news.'));
            }			
		}
        
        return $resultRedirect->setPath('*/*/edit', ['id' => $consignment]);
    }
}
