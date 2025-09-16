<?php
	namespace TiDesign\Consignment\Controller\Adminhtml\Consignmentlist;

	use Magento\Backend\App\Action;
	use Magento\Catalog\Api\CategoryRepositoryInterface;
	use Magento\Catalog\Api\ProductRepositoryInterface;

	use Magento\Framework\Controller\ResultFactory;
	use Magento\Backend\App\Action\Context;
	use Magento\Framework\Exception\LocalizedException;
	use Magento\Ui\Component\MassAction\Filter;
	use TiDesign\Consignment\Model\ResourceModel\Consignmentlist\CollectionFactory;

	use TiDesign\Consignment\Model\RemoveFromCategory;
    use Magento\Store\Model\StoreManagerInterface;
    use Magento\Indexer\Model\IndexerFactory;

    use Magento\InventoryCatalogApi\Api\BulkSourceUnassignInterface;

	/**
	 * Class MassDelete
	 */
	class Removeproducts extends Action
	{

		protected $filter;
		protected $collectionFactory;
		protected $cacheManager;
		protected $request;
		protected $formKey;
		protected $categoryRepository;
		protected $productRepository;


        /**
         * @var BulkSourceUnassignInterface
         */
        private $bulkSourceUnassign;
		protected $removeFromCategory;

		private 	$consignmentlistRepository;
        private $storemanager;
        private IndexerFactory $indexerFactory;
        private \Magento\Indexer\Model\Indexer\CollectionFactory $indexCollection;

        /**
		 * @param Context $context
		 * @param Filter $filter
		 * @param CollectionFactory $collectionFactory
		 */
		public function __construct(
			Context $context,
			Filter $filter,
			CollectionFactory $collectionFactory,
			RemoveFromCategory $removeFromCategory,
			\Magento\Framework\App\Cache\Manager $cacheManager,
			\Magento\Framework\Data\Form\FormKey $formKey,
			\Magento\Framework\App\Request\Http $request,
			\TiDesign\Consignment\Api\ConsignmentlistRepositoryInterface $consignmentlistRepository = null,
			CategoryRepositoryInterface $categoryRepository,
			ProductRepositoryInterface $productRepository,
            StoreManagerInterface $storemanager,
            IndexerFactory $indexerFactory,
            \Magento\Indexer\Model\Indexer\CollectionFactory $indexCollection,
            BulkSourceUnassignInterface $bulkSourceUnassign
		){
			$this->filter = $filter;
			$this->collectionFactory = $collectionFactory;
			$this->cacheManager = $cacheManager;
			$this->consignmentlistRepository = $consignmentlistRepository
				?: \Magento\Framework\App\ObjectManager::getInstance()->get(\TiDesign\Consignment\Api\ConsignmentlistRepositoryInterface::class);
			$this->request = $request;
			$this->formKey = $formKey;
			$this->request->setParam('form_key', $this->formKey->getFormKey());
			$this->categoryRepository = $categoryRepository;

			$this->removeFromCategory = $removeFromCategory;

			$this->productRepository = $productRepository;
            $this->storemanager = $storemanager;
            $this->indexerFactory = $indexerFactory;
            $this->indexCollection = $indexCollection;
            $this->bulkSourceUnassign = $bulkSourceUnassign;
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

            $idCollection = $postData['disable_id'] ?? $postData['enable_id'];
         //   print_r($idCollection);die();
			if (!empty($postData)) {
				if($consignment){
					if(count($idCollection)){
						$data['td_id']	= $consignment;



						try {
							$model      = $this->consignmentlistRepository->getById($consignment);

							$skus       = [];
							$sources[]  = $model->getTdSource();
                            $websiteId  = $model->getTdWebsite();
							$categoryId = $model->getTdCategory();
							$productIds = array_filter($idCollection);

                            $stores = $this->storemanager->getWebsite($websiteId)->getStores();
                            $storeId =array_values($stores)[0]->getStoreId();

							foreach  ($productIds as $productId) {
								$this->removeFromCategory->removeByIds($categoryId, $productId, $storeId);
                                $product = $this->productRepository->getById($productId);
                                $skus[] = $product->getSku();
							}

                            /*
                             * TODO vendor/magento/moduel-inventory-catalog/Model/ResourceModel/BulkSourceUnassign.php Magento\InventoryCatalogApi\Api\BulkSourceUnassignInterface
                             * module-inventory-catalog-admin-ui\Controller\Adminhtml\Source\BulkUnassignPost.php
                             *
                             */

                            $count = $this->bulkSourceUnassign->execute($skus, $sources);

                            /*
                             * INDEXER
                            */
                            $indexerCollection = $this->indexCollection->create();
                            $indexIds = $indexerCollection->getAllIds();

                            foreach ($indexIds as $indexId)
                            {
                                $indexIdArray = $this->indexerFactory->create()->load($indexId);

                                //If you want reindex all use this code.
                               $indexIdArray->reindexAll($indexId);

                                //If you want to reindex one by one, use this code
                               // $indexIdArray->reindexRow($indexId);
                            }

                            } catch (LocalizedException $e) {
							$this->messageManager->addErrorMessage(__('This record no longer exists.') . $e->getMessage());
							return $resultRedirect->setPath('*/*/');
						} catch (\Exception $e) {
							//$this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the news.'));
                            echo $e->getMessage();die();
						}
					}
				}


			}

			return $resultRedirect->setPath('*/*/edit', ['id' => $consignment]);
		}


	}
