<?php
namespace TiDesign\CacheCreator\Observer;

use Magento\Framework\Event\ObserverInterface;

class Clearcache implements ObserverInterface
{
	private 	$_emulation;
	private 	$_storeManager;
	private 	$_state;
	private 	$_swMenu;
    protected 	$_logger;
    protected 	$_dir;
    protected 	$_io;
	
    
    public function __construct(  
		\Magento\Store\Model\App\Emulation $Emulation,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\App\State $state,
		\TiDesign\CacheCreator\Block\Topmenu $swMenu,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
		\Magento\Framework\Filesystem\Io\File $io,
		\Magento\Framework\Filesystem\DirectoryList $dir,
		\TiDesign\Consignment\Logger\Logger $customLogger
    ) {
		$this->_emulation	= $Emulation;
		$this->_storeManager= $storeManager;
		$this->_state		= $state;
		$this->_swMenu		= $swMenu;
        $this->fileDriver 	= $fileDriver;    
		$this->_dir 		= $dir;	
		$this->_io 			= $io;	
		$this->_logger		= $customLogger;		
    }
 
    public function execute(\Magento\Framework\Event\Observer $observer)
    {    
		$cacheFolder = $this->_dir->getPath('pub').'/anasayfa/cache/';
		try{	
			$stores = $this->_storeManager->getStores();
			foreach($stores as $store){
				$storeId = $store->getData('store_id'); 			
				if(is_dir($cacheFolder.$storeId)) {
					$this->_io->rmdir($cacheFolder.$storeId);
				}
				$this->_io->checkAndCreateFolder($cacheFolder.$storeId);
//				$this->_emulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);
				//$this->_storeManager->setCurrentStore($storeId);
				$menu = $this->_swMenu->getFrontendMenu($storeId);
				file_put_contents($cacheFolder.$storeId.'/menu.html', $menu);
				
//				$this->_emulation->stopEnvironmentEmulation();			
			}
		}catch(Exception $e) {
			$this->_logger->error('Message: ' .$e->getMessage());
			$this->_logger->error( $e->getLine());
			$this->_logger->error( "Error in this file: " . $e->getFile());
			$this->_logger->error($e->getTrace());
		}		
    }
    
}