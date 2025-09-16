<?php
namespace Smartwave\Porto\Block\Catalog;

class ProductImage extends \Magento\Catalog\Block\Product\Image
{

	public function getProductSmallImage(){
		$p_id 			= $this->getProductId();
		$objectManager 	= \Magento\Framework\App\ObjectManager::getInstance();
		$imageHelper  	= $objectManager->get('Magento\Catalog\Helper\Image');
		$product 		= $objectManager->create('Magento\Catalog\Model\Product')->load($p_id);
	
		$url = $imageHelper->init($product, 'product_thumbnail_image')->getUrl();
		return $url;
	}
}