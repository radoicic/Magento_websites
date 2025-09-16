<?php
	namespace TiDesign\Consignment\Controller\Ajax;
	
	use Magento\Framework\App\Action\Action;
	use Magento\Framework\App\ResponseInterface;
	use Magento\Framework\Controller\ResultFactory;
	use Magento\Framework\App\Config\ScopeConfigInterface;
	use Magento\Store\Model\Store;

	class Download extends \Magento\Framework\App\Action\Action
	{
		protected 	$resultPageFactory;
		protected 	$consignmentHelper;
		protected 	$sourceHelper;
		private 	$productRepository; 
		
		private 	$spwriter;
		private 	$Spreadsheet;
		
		protected 	$productFactory;
		protected 	$_storeManager;
		private 	$_consCollection;
		protected   $_customerSession;
		protected   $imageHelper;
		
		public function __construct(
			\Magento\Framework\App\Action\Context $context,
			\PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet,
			\PhpOffice\PhpSpreadsheet\Writer\Xls $xlswriter,
			\TiDesign\Consignment\Helper\Consignment $consignmentHelper,
			\TiDesign\Consignment\Helper\InventorySource $sourceHelper,
			\TiDesign\Consignment\Model\ConsignmentlistFactory $consCollection,
			\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
			\Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
			\Magento\Catalog\Model\ProductFactory $productFactory,
			\Magento\Store\Model\StoreManagerInterface $storeManager,
			\Magento\Customer\Model\Session $customerSession,
			\Magento\Catalog\Helper\Image $imageHelper
		) {
			parent::__construct($context);
			$this->spwriter 				= $xlswriter;
			$this->Spreadsheet 				= $spreadsheet;
			$this->consignmentHelper 		= $consignmentHelper;
			$this->sourceHelper 			= $sourceHelper;
			$this->resultJsonFactory 		= $resultJsonFactory;
			$this->productRepository 		= $productRepository;
			$this->productFactory 			= $productFactory;
			$this->_storeManager 			= $storeManager;
			$this->_consCollection 			= $consCollection;
			$this->_customerSession         = $customerSession;
			$this->imageHelper         		= $imageHelper;
		}
		public function execute() {
			
			$spreadsheet = new $this->Spreadsheet();
			
			$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
			
			$spreadsheet->getActiveSheet()->getPageMargins()
						->setLeft(0.1)
						->setRight(0.1)
						->setTop(0.9)
						->setBottom(0.9)
						->setHeader(0);
			
			$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(5, 'px');
			$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(30, 'px');
			$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(100, 'px');
			$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(300, 'px');
			$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(60, 'px');
			$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(65, 'px');
			$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(65, 'px');
			$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(5, 'px');
			$spreadsheet->getActiveSheet()->getDefaultRowDimension()->setRowHeight(60, 'px');
			$spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(5, 'px');
			
			$spreadsheet->setActiveSheetIndex(0)
				->setCellValue('B2', '#')
				->setCellValue('C2', 'SKU')
				->setCellValue('D2', 'NAME')
				->setCellValue('F2', 'SUPPLIED QTY')	
				->setCellValue('G2', 'QTY SOLD');	
							
			$styleHeader = [
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
					'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
					'wrapText' => true,
				],
			    'font' => [
					'bold' => true,
					'size' => 11,
				],
				'borders' => [
					'allBorders' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
						'color' => ['rgb' => '000000'],
					],
				],
				'fill' => [
					'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
					'color' => ['rgb' => 'D9E1F2'],
				],
			];

			$spreadsheet->getActiveSheet()->getStyle('B2:G3')->applyFromArray($styleHeader);
			
			$spreadsheet->setActiveSheetIndex(0)->setCellValue('B3', 'QTY SOLD = SUPPLIED QTY-MINUS-ON SELF STOCK '. "\n". '( Using the claronz website portal process the QTY SOLD throught the cart )');
			$spreadsheet->getActiveSheet()->getStyle('B3')->getAlignment()->setWrapText(true);
			$spreadsheet->getActiveSheet()->mergeCells('B3:G3');

			$styleCell = [
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
					'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
					'wrapText' => true,
				],
			    'font' => [
					'bold' => false,
					'size' => 10,
				],
				'borders' => [
					'allBorders' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
						'color' => ['rgb' => '000000'],
					],
				],
			];
			
			$productCollection = $this->getCollection();
			$i = 1;
			$s = 4;
			foreach($productCollection as $product) {
/***********/
					
				$spreadsheet->setActiveSheetIndex(0)
					->setCellValue('B'.$s, $i++)
					->setCellValue('C'.$s, $product->getSku())
					->setCellValue('D'.$s, $product->getName())
					->setCellValue('F'.$s, $product->getQty());				
					
				$image_url = $this->imageHelper->init($product, 'product_thumbnail_image')->getUrl();
//				if (file_exists($image_url)) {
					$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
					$drawing->setName('product image'.$s);
					$drawing->setDescription('product image'.$s);
					$drawing->setPath($image_url); /* put your path and image here */
					$drawing->setCoordinates('E'.$s);
					$drawing->setHeight(50);
					$drawing->setOffsetX(5);
					$drawing->setOffsetY(5);
					$drawing->setWorksheet($spreadsheet->getActiveSheet());				
/*				}else{
					$spreadsheet->setActiveSheetIndex(0)
					->setCellValue('E'.$s, $image_url);	
					
				}
*/
				$spreadsheet->getActiveSheet()->getStyle('B'.$s.':G'.$s)->applyFromArray($styleCell);
				 //break;
				 $s++;
			}
			
/***********/




				
			// Redirect output to a client’s web browser (Xls)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Consignment Stock -'.gmdate('d M Y H:i') .'.xls"');
			header('Cache-Control: max-age=0');
			// If you're serving to IE 9, then the following may be needed
			header('Cache-Control: max-age=1');

			// If you're serving to IE over SSL, then the following may be needed
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
			header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
			header('Pragma: public'); // HTTP/1.0

			
			$writer = new $this->spwriter($spreadsheet);
			$writer->save('php://output');
			exit;
		}
		
		private function getCollection(){
			$store = $this->_storeManager->getStore();
			$collection = $this->productFactory->create()->getCollection()->addAttributeToSelect(
				'*'
			)->setStore(
				$store
			);			
			
			$customerId = $this->_customerSession->getId();
			$data 	= $this->_consCollection->create()->getCollection();
			$data->addFieldToFilter('td_customer', ['eq'=>$customerId]);
			if($data->getSize()){
				$dd = $data->getFirstItem()->toArray();
				$categoryId = $dd["td_category"];
			}
		
			if($categoryId){
				$collection->addCategoriesFilter(['in' => $categoryId]);
			}			
			
			$source = $this->consignmentHelper->getConsSource($categoryId);

			$collection->joinField(
				'qty',
				'inventory_source_item',
				'quantity',
				'sku=sku',
				'{{table}}.source_code="'.$source.'"',
				'left'
			);
			if ($store->getId()) {
				$collection->setStoreId($store->getId());
				$collection->addStoreFilter($store);
				$collection->joinAttribute(
					'name',
					'catalog_product/name',
					'entity_id',
					null,
					'inner',
					Store::DEFAULT_STORE_ID
				);
				$collection->joinAttribute(
					'status',
					'catalog_product/status',
					'entity_id',
					null,
					'inner',
					$store->getId()
				);
				$collection->joinAttribute(
					'visibility',
					'catalog_product/visibility',
					'entity_id',
					null,
					'inner',
					$store->getId()
				);
				$collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
			} else {
				$collection->addAttributeToSelect('price');
				$collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
				$collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
			}
			
			return $collection;
		}
		
	}