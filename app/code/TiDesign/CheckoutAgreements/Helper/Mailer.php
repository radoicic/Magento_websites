<?php
namespace TiDesign\CheckoutAgreements\Helper;

use Psr\Log\LoggerInterface;
use Magento\Framework\App\Area;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\MailException;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use TiDesign\CheckoutAgreements\Model\Mail\Template\TransportBuilder;
use TiDesign\CheckoutAgreements\Helper\Data;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Mailer extends AbstractHelper
{
    const EMAIL_TEMPLATE 	= 'checkoutagreements/checkoutagreements_mail/email_template';
    const EMAIL_TEXT 		= 'checkoutagreements/checkoutagreements_mail/email_text';
    const EMAIL_FROM 		= 'checkoutagreements/checkoutagreements_mail/sender_email_identity';
    const EMAIL_NAME 		= 'checkoutagreements/checkoutagreements_mail/sender_name';
    const EMAIL_BCC			= 'checkoutagreements/checkoutagreements_mail/bcc';
	
	const EMPTY_TEMPLATE 	= 'checkoutagreements_checkoutagreements_mail_message_template';
	
	protected $_customerRepositoryInterface;
    /**
     * @var StateInterface
     */
    private $inlineTranslation;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var LoggerInterface
     */
    private $logger;
	
	private $filesystem;
	
	protected $scopeConfig;

	protected 	$_dataHelper;   
	
    /**
     * Data constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context 				$context,
        StoreManagerInterface 	$storeManager,
        TransportBuilder 		$transportBuilder,
		Filesystem 				$filesystem,
        StateInterface 			$inlineTranslation,
		CustomerRepositoryInterface $customerRepositoryInterface,
		ScopeConfigInterface 	$scopeConfig,
        LoggerInterface 		$logger,
		Data 					$dataHelper
    )
    {
        $this->storeManager 				= $storeManager;
        $this->transportBuilder 			= $transportBuilder;
		$this->filesystem 					= $filesystem;
        $this->inlineTranslation 			= $inlineTranslation;
		$this->scopeConfig 					= $scopeConfig;
        $this->logger 						= $logger;
		$this->_customerRepositoryInterface = $customerRepositoryInterface;
		$this->_dataHelper 					= $dataHelper;
        parent::__construct($context);
    }

    /**
     * Send Confirmation Mail
     *
     * @return $this
     *
     * @throws LocalizedException
     * @throws MailException
     */
    public function sendConfirmationMail($customerId, $customer_confirmation, $fileName)
    {
		$customer = $this->_customerRepositoryInterface->getById($customerId);

        
		$email 		= $customer->getEmail();
        $template 	= $this->getConfig(self::EMAIL_TEMPLATE);
        $email_text = $this->getConfig(self::EMAIL_TEXT);
		$from_email = $this->getConfig(self::EMAIL_FROM);
		$from_name	= $this->getConfig(self::EMAIL_NAME);
        $from 		= ['email' => $from_email, 'name' => $from_name] ;

		$bcc 		= array_map('trim', explode(',', $this->getConfig(self::EMAIL_BCC)));

        $vars = [
            'customer_name' => $customer->getFirstname() .' '.$customer->getLastname() ,
            'email_text' => $email_text,
			'customer_confirmation' => $customer_confirmation,
            'store' => $this->getStore(),
			'customer_id' => $customerId
        ];
		$options = 	[
			'area' => Area::AREA_FRONTEND,
			'store' => $this->getStoreId()
		];		
		$mediaPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
		$filePath 	= $mediaPath . 'TiDesign/contracts/'.$fileName;		
		$storeId = $this->getStoreId();
		
        $this->inlineTranslation->suspend();

        $transport = $this->transportBuilder
			->setTemplateIdentifier( $template )
			->setTemplateOptions($options)
			->setTemplateVars($vars)
			->setFrom($from)
			->addTo($email)
			->addBcc($bcc)
			->addAttachment(file_get_contents($filePath),$fileName ,'application/pdf')
			->getTransport();

        try {
            $transport->sendMessage();
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage());
        }
        $this->inlineTranslation->resume();

        return $this;
    }
	public function sendDenyMail($customerId, $fileName, $message){
		$customer 	= $this->_customerRepositoryInterface->getById($customerId);
		$email 		= $customer->getEmail();
		$template 	= self::EMPTY_TEMPLATE;
		$email_text = "The terms and conditions form you submitted was rejected. Please re-apply after checking and making sure that all fields are filled.<br>".$message."<br><br><br>";
		$from_email = $this->getConfig(self::EMAIL_FROM);
		$from_name	= $this->getConfig(self::EMAIL_NAME);
        $from 		= ['email' => $from_email, 'name' => $from_name] ;	
		
		$bcc 		= array_map('trim', explode(',', $this->getConfig(self::EMAIL_BCC)));
		
        $vars = [
            'customer_name' => $customer->getFirstname() .' '.$customer->getLastname() ,
            'email_text' => $email_text,
            'store' => $this->getStore(),
			'customer_id' => $customerId,
			'email_subject' => 'Terms and conditions form rejected!!'
        ];
		$options = 	[
			'area' => Area::AREA_FRONTEND,
			'store' => $this->getStoreId()
		];		
		$mediaPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
		$filePath 	= $mediaPath . 'TiDesign/contracts/'.$fileName;		
		$storeId = $this->getStoreId();
		
        $this->inlineTranslation->suspend();

        $transport = $this->transportBuilder
			->setTemplateIdentifier( $template )
			->setTemplateOptions($options)
			->setTemplateVars($vars)
			->setFrom($from)
			->addTo($email)
			->addBcc($bcc)
			->addAttachment(file_get_contents($filePath),$fileName ,'application/pdf')
			->getTransport();

        try {
            $transport->sendMessage();
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage());
        }
        $this->inlineTranslation->resume();

        return $this;		
	}
	
	public function sendEditAcceptMail($customerId)
	{
		$customer 	= $this->_customerRepositoryInterface->getById($customerId);
		$email 		= $customer->getEmail();
		$template 	= self::EMPTY_TEMPLATE;
		$email_text = "Admin accepted your edit request. You can edit your terms and conditions form from your account page.<br><br><br>";
		$from_email = $this->getConfig(self::EMAIL_FROM);
		$from_name	= $this->getConfig(self::EMAIL_NAME);
        $from 		= ['email' => $from_email, 'name' => $from_name] ;	
		
		$bcc 		= array_map('trim', explode(',', $this->getConfig(self::EMAIL_BCC)));
		
        $vars = [
            'customer_name' => $customer->getFirstname() .' '.$customer->getLastname() ,
            'email_text' => $email_text,
            'store' => $this->getStore(),
			'customer_id' => $customerId,
			'email_subject' => 'Terms and conditions edit request accepted'
        ];
		$options = 	[
			'area' => Area::AREA_FRONTEND,
			'store' => $this->getStoreId()
		];		
		
        $this->inlineTranslation->suspend();

        $transport = $this->transportBuilder
			->setTemplateIdentifier( $template )
			->setTemplateOptions($options)
			->setTemplateVars($vars)
			->setFrom($from)
			->addTo($email)
			->addBcc($bcc)
			->getTransport();

        try {
            $transport->sendMessage();
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage());
        }
        $this->inlineTranslation->resume();

        return $this;		
	}
	public function sendEditDenyMail($customerId, $message){
		$customer 	= $this->_customerRepositoryInterface->getById($customerId);
		$email 		= $customer->getEmail();
		$template 	= self::EMPTY_TEMPLATE;
		$email_text = "Admin rejected your edit request.<br>".$message."<br><br><br>";
		$from_email = $this->getConfig(self::EMAIL_FROM);
		$from_name	= $this->getConfig(self::EMAIL_NAME);
        $from 		= ['email' => $from_email, 'name' => $from_name] ;	
		
		$bcc 		= array_map('trim', explode(',', $this->getConfig(self::EMAIL_BCC)));
		
        $vars = [
            'customer_name' => $customer->getFirstname() .' '.$customer->getLastname() ,
            'email_text' => $email_text,
            'store' => $this->getStore(),
			'customer_id' => $customerId,
			'email_subject' => 'Editing terms and conditions form request rejected!!'
        ];
		$options = 	[
			'area' => Area::AREA_FRONTEND,
			'store' => $this->getStoreId()
		];		
		
        $this->inlineTranslation->suspend();

        $transport = $this->transportBuilder
			->setTemplateIdentifier( $template )
			->setTemplateOptions($options)
			->setTemplateVars($vars)
			->setFrom($from)
			->addTo($email)
			->addBcc($bcc)
			->getTransport();

        try {
            $transport->sendMessage();
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage());
        }
        $this->inlineTranslation->resume();

        return $this;		
	}
	public function sendAdminInfoMail(){
		$email 		= $this->getConfig(self::EMAIL_FROM);
		$template 	= self::EMPTY_TEMPLATE;
		$email_text = "New request from customer. Please check logs to accep/deny form<br><br><br>";
		$from_email = $this->getConfig(self::EMAIL_FROM);
		$from_name	= $this->getConfig(self::EMAIL_NAME);
        $from 		= ['email' => $from_email, 'name' => $from_name] ;	
		
		$admin_email	= $this->getConfig('trans_email/ident_'.$email.'/email');
		
        $vars = [
            'customer_name' => 'Admin' ,
            'email_text' => $email_text,
            'store' => $this->getStore(),
			'customer_id' => '',
			'email_subject' => 'New request from customer'
        ];
		$options = 	[
			'area' => Area::AREA_FRONTEND,
			'store' => $this->getStoreId()
		];		
		
        $this->inlineTranslation->suspend();

        $transport = $this->transportBuilder
			->setTemplateIdentifier( $template )
			->setTemplateOptions($options)
			->setTemplateVars($vars)
			->setFrom($from)
			->addTo($admin_email)
			->getTransport();

        try {
            $transport->sendMessage();
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage());
        }
        $this->inlineTranslation->resume();

        return $this;		
	}

    /*
     * get Current store id
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /*
     * get Current store Info
     */
    public function getStore()
    {
        return $this->storeManager->getStore();
    }
	public function getConfig($config_path)
    {
		$storeId = $this->getStoreId();
		return $this->scopeConfig->getValue(
            $config_path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}