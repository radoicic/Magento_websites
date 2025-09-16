<?php

	use Magento\Framework\App\Bootstrap;
	use Magento\Store\Model\StoreManager;
	use Magento\Store\Model\ScopeInterface;

	ini_set('display_errors', '1');
	ini_set('display_startup_errors', '1');
	error_reporting(E_ALL);
	require __DIR__ . '/../../app/bootstrap.php';
	
	$params = $_SERVER;
	
	switch ($_SERVER['HTTP_HOST']) {
		default:
		case 'nzboxer.com':
		case 'www.nzboxer.com':
			$x = 'base';
		break;
		case 'claronz.com':
		case 'www.claronz.com':
			$x = 'claro';
		break;
	}
	$params[StoreManager::PARAM_RUN_CODE] = $x;
	$params[StoreManager::PARAM_RUN_TYPE] = 'website';
	
	$bootstrap = Bootstrap::create(BP, $params);
	$objectManager = $bootstrap->getObjectManager();
	$state = $objectManager->get('Magento\Framework\App\State');
	$state->setAreaCode('frontend');
	$appEmulation = $objectManager->get('\Magento\Store\Model\App\Emulation');
	$storeId  = $objectManager->get('\Magento\Store\Model\StoreManagerInterface')->getStore()->getId();
/****************************************************************************************************************/
/*	$dir            = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
		$cacheFolder    = $dir->getPath('pub').'/anasayfa/cache/';
		$io             = $objectManager->get('\Magento\Framework\Filesystem\Io\File');
		
		try{
			$stores = $objectManager->get('\Magento\Store\Model\StoreManagerInterface')->getStores();
			foreach($stores as $store){
				$storeIds  = $store->getData('store_id');
				if(is_dir($cacheFolder.$storeIds)) {
					$io->rmdir($cacheFolder.$storeIds);
				}
				$io->checkAndCreateFolder($cacheFolder.$storeIds);
				$appEmulation->startEnvironmentEmulation($storeIds, \Magento\Framework\App\Area::AREA_FRONTEND, true);
				$swMenu         = $objectManager->get('\Smartwave\Megamenu\Block\Topmenu');
				$menu = $swMenu->getMegamenuHtml();
				file_put_contents($cacheFolder.$storeIds.'/menu.html', $menu);
	
				//slider
			$homePage = $objectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface')->getValue("web/default/cms_home_page", ScopeInterface::SCOPE_STORE,$storeIds);
			echo $homePage."<br>";
			
			$content = $objectManager->create('\Magento\Cms\Model\Page');
			$myCmsData = $content->load($homePage, 'identifier');
			$cmsContent = $objectManager->get('\Magento\Cms\Model\Template\FilterProvider')->getPageFilter()->filter($myCmsData->getContent());

			$dom = new DomDocument();
			@$dom->loadHTML($cmsContent);
			$xpath = new DOMXpath($dom);
			$xpathQuery="//div[@id='banner-slider-demo-14']/div/a/img";

			$elements = $xpath->query($xpathQuery);
			$slider ='';
			if (!is_null($elements)) {
				foreach ($elements as $element) {
					$link = $element->parentNode->getAttribute("href");
					$img = $element->getAttribute("src");
					//$slider .='<div class="slide"><a class="slider-link" href="$link"><img src="$img" alt=""></a></div>';
					echo $link."*--".$img."<br>";
				}
			}
			//file_put_contents($cacheFolder.$storeIds.'/slider.html', $slider);
			


			$appEmulation->stopEnvironmentEmulation();


			
		}
	}catch(Exception $e) {
		echo $e->getTrace();
	}
*/

	/****************************************************************************************************************/
?>
<!doctype html>
<html lang="en">
	<?php require_once ("assets/html/{$storeId}/head.html");?>
	<body>
		<header class="page-header">
			<?php require_once ("assets/html/{$storeId}/header.html");?>
			<div class="nav">
				<nav class="navigation sw-megamenu " role="navigation">
					<ul><?php require_once ("cache/$storeId/menu.html")	?></ul>
				</nav>
			</div>
			
		</header>
		<main id="main-content" class="page-main">
			<div class="container">
				<div class="slider"><?php //require_once ("cache/$storeId/slider.html")?></div>
				<div class="banners"></div>
				<div class="products-slider"></div>
				<div class="instagram"></div>
			</div>
		</main>
		<footer>
			<div class="footer-main"></div>
			<div class="footer-bottom"></div>
		</footer>

		<dialog data-modal>
			<div class="modal-deader"></div>
			<div class="modal-content">Are you sure you would like to remove this item from the shopping cart?</div>
			<div class="modal-footer">
				<button class="action-secondary action-dismiss" type="button"><span>Cancel</span>
				</button><button class="action-primary action-accept" type="button"><span>OK</span></button>
			</div>
		</dialog>
	</body>
</html>