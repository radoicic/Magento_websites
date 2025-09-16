<?php
namespace TiDesign\CheckoutAgreements\Controller\Contract;

use Magento\Framework\Controller\ResultFactory;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Index extends \Magento\Framework\App\Action\Action
{

	protected $_dir;
	protected $_filesystem;
	protected $_pageFactory;
	protected $_dataFactory;
	protected $_logFactory;
	protected $_remoteAddress;
	protected $_timezoneInterface;
	protected $_mailHelper;
	protected $_dataHelper; 

	public function __construct(
		\Magento\Framework\App\Action\Context 			$context,
		\Magento\Framework\View\Result\PageFactory 		$pageFactory,
		\TiDesign\CheckoutAgreements\Model\DataFactory 	$dataFactory,
		\TiDesign\CheckoutAgreements\Model\LogFactory 	$logFactory,
		\TiDesign\CheckoutAgreements\Helper\Mailer	 	$mailHelper,
		\TiDesign\CheckoutAgreements\Helper\Data	 	$dataHelper,
		\Magento\Framework\Filesystem 					$filesystem,
		DirectoryList 									$dir,
		RemoteAddress									$remoteAddress,
		TimezoneInterface 								$timezoneInterface
	){
		$this->_pageFactory 		= $pageFactory;
		$this->_dataFactory 		= $dataFactory;
		$this->_logFactory 			= $logFactory;
		$this->_dir 				= $dir;
		$this->_filesystem 			= $filesystem;
		$this->_remoteAddress 		= $remoteAddress;
		$this->_timezoneInterface 	= $timezoneInterface;
		$this->_mailHelper 			= $mailHelper;
		$this->_dataHelper 			= $dataHelper;
		return parent::__construct($context);
	}

    public function execute()
    {
        $postData = (array) $this->getRequest()->getPost();

        if (!empty($postData)) {
			$resultRedirect = $this->resultRedirectFactory->create();
			$data = null;

			if (!empty($postData['id'])) {
				$data['id']   			= $postData['id']; 
			}
            $data['customer_id']   	= $postData['customer_id'];
			$data['signature']		= $postData['signature'];
			if(isset($postData['photo'])){
				$data['photo']			= $postData['photo'];
			}
			/* ip tarih saat*/
			$data['creation_ip'] 	= $this->_remoteAddress->getRemoteAddress();
			$data['date'] 			= $this->_timezoneInterface->date()->format('Y-m-d H:i');
			$data['activation_code']= $this->salt();
			$data['edit']			= 0;
			
			
			unset($postData['id']);
			unset($postData['customer_id']);
			unset($postData['signature']);
			unset($postData['photo']);
			unset($postData['form_key']);
			$data['form_data']		= json_encode($postData);
			

			$model = $this->_dataFactory->create();
			$model->setData($data)->save();
			$this->messageManager->addSuccessMessage(__("Data Saved Successfully."));
			
			$filename = $this->createPdf( $data['customer_id']);
			
			$html = $this->_dataHelper->getData($data['customer_id']);
			$model->setData('html_code', $html)->save();
			$model->setData('filename', $filename)->save();
			
			
			/* log */
			$dataLog['customer_id'] 	= $data['customer_id'];
			$dataLog['message'] 		= "Customer filled out the Terms and Conditions form and submited";
			$dataLog['creation_ip'] 	= $this->_remoteAddress->getRemoteAddress();
			$dataLog['date'] 			= $this->_timezoneInterface->date()->format('Y-m-d H:i');
			$dataLog['status']			= 0;			
			$dataLog['operation']		= 1;			
			$dataLog['filename'] 		= $filename;
			
			$logModel = $this->_logFactory->create();
			$logModel->setData($dataLog)->save();
			
			

			$this->_mailHelper->sendAdminInfoMail();
			
			return $resultRedirect->setPath('*/*/');
        }

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
	public function createPdf($customer_id){
		require_once($this->_dir->getPath('lib_internal'). '/TCPDF/TCPDF.php');
		$mediaPath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();

		
		$tcpdf = new \TCPDF_TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$tcpdf->SetCreator(PDF_CREATOR);
		$tcpdf->SetTitle('Title');
		$tcpdf->setPrintHeader(false);
		$tcpdf->setPrintFooter(true);	
		$tcpdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		$tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$tcpdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$tcpdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$tcpdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		$tcpdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
			require_once(dirname(__FILE__) . '/lang/eng.php');
			$tcpdf->setLanguageArray($l);
		}
		$tcpdf->setFontSubsetting(true);
		

/*		
		$resultPage = $this->_pageFactory->create();
		$blockData	= $resultPage->getLayout()
								->createBlock('Magento\Cms\Block\Block')
								->setBlockId('deneme')
								->toHtml();
*/		

		//echo $customer_id;die();
		$blockData = $this->_dataHelper->getData($customer_id);
//echo $blockData;

		$needles = array("\r\n");
		$replacement = "<br />";
 		$html = str_replace($needles, $replacement, $blockData);

		$matches = preg_split('/(<img[^>]+\>)/i', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
		
		$lg 	= Array();
		$lg['a_meta_charset'] = 'UTF-8';
		$tcpdf->setLanguageArray($lg);
		$tcpdf->SetFont('freesans', '', 10);
		$tcpdf->AddPage();
		
		foreach ($matches as &$value) {
			
			if(str_starts_with($value,"<img")){
				preg_match('/src=\'data:image\/[^;]+;base64,([^"]+)\' style/i', $value, $ss);

if(sizeof($ss)>0){
				$src = $ss[1];
				$imgdata = base64_decode($src);
				$tcpdf->writeHTML("<div>", true, false, true, false, '');
				//$tcpdf->Image('@'.$imgdata, "","" , 60,"" , '', '', '', true, 150, '', false, false, 1, false, false, false);
				$fitbox = 'L';
				$fitbox[1] = 'T';
				$tcpdf->Image('@'.$imgdata, "", "", 100, 30, '', '', '', false, 150, '', false, false, 1, $fitbox, false, false);
				$tcpdf->writeHTML("</div>", true, false, true, false, '');
				//$tcpdf->writeHTML("<p></p>", true, false, true, false, '');
}
			}else{
				$tcpdf->writeHTML($value, true, false, true, false, '');
			}
		}
		$tcpdf->lastPage();
		$name 		= 'Contract_'. time().'.pdf';
		$filename 	= $mediaPath . 'TiDesign/contracts/'.$name;
		$tcpdf->Output($filename, 'F');
		
		return $name;
	}
	public static function salt(int $length = 32): string
	{
		return bin2hex(openssl_random_pseudo_bytes($length));
	}	
}