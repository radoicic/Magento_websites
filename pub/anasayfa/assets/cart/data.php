<?php
	ini_set('display_errors', '1');
	ini_set('display_startup_errors', '1');
	error_reporting(E_ALL);

	
	try{
		require __DIR__ . '/../../../../app/bootstrap.php';
	    $params = $_SERVER;
		$params[\Magento\Store\Model\StoreManager::PARAM_RUN_CODE] = 'base';
	    $params[\Magento\Store\Model\StoreManager::PARAM_RUN_TYPE] = 'website';
	    $bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $params);
		$objectManager = $bootstrap->getObjectManager();
		$state = $objectManager->get('Magento\Framework\App\State');
		$state->setAreaCode('frontend');
		$appEmulation = $objectManager->get('\Magento\Store\Model\App\Emulation');
		$storeId  = $objectManager->get('\Magento\Store\Model\StoreManagerInterface')->getStore()->getId();
		$appEmulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);
		
		
		
		$helper = $objectManager->get('\Magento\Checkout\Helper\Cart');
		$quote = $helper->getQuote();
		
		echo "<pre>"; print_r($quote->debug());echo "</pre>";
		
		
		$summary_count = (int)$quote->getItemsQty();
		$subtotalAmount = number_format((float)$quote->getGrandTotal(), 2, '.', ',');
	
		$c_empty    = '<strong class="subtitle empty">You have no items in your shopping cart.</strong>';
		$c_total    = '<div class="subtotal"><span class="label"><span>Subtotal</span></span><div class="amount price-container"><span class="price-wrapper"><span class="price">NZ$'.$subtotalAmount.'</span></span></div></div>';
		$c_button   = '<div class="actions"><button id="top-cart-btn-checkout" type="button" class="action primary checkout" title="Go to Checkout">Go to Checkout</button></div>';
		
		$customerDataItem = $objectManager->get('\Magento\Checkout\CustomerData\DefaultItem');
		$itemRepository = $objectManager->get('\Magento\Quote\Api\CartItemRepositoryInterface');
		$productModel = $objectManager->get('\Magento\Catalog\Api\ProductRepositoryInterface');
		$taxHelper = $objectManager->get('\Magento\Catalog\Helper\Data');
		
		$items = $itemRepository->getList($quote->getEntityId());
		$listItem="";
		foreach ($items as $cartItem) {
			
			$allData = $customerDataItem->getItemData($cartItem);
			$product = $productModel->getById($allData['product_id']);
			$imageHelper = $objectManager->get('\Magento\Catalog\Helper\Image');
			$productImageUrl = $imageHelper->init($product, 'mini_cart_product_thumbnail')->getUrl();
			$product_options = "";
			foreach ($allData['options'] as $opt){
				$product_options .= '<dt class="label">'.$opt['label'].'</dt><dd class="values">'.$opt['value'].'</dd>';
			}
			$product_price = number_format($taxHelper->getTaxPrice($product, $allData['product_price_value'], true),2);
			
			
			$listItem .= <<<HTML
				<li class="item product product-item">
					<div class="product">
						<a href="{$allData['product_url']}" title="{$allData['product_name']}" tabindex="-1" class="product-item-photo">
							<span class="product-image-container">
								<span class="product-image-wrapper"><img class="product-image-photo" src="{$productImageUrl}" alt="{$allData['product_image']['alt']}"></span>
							</span>
						</a>
						<div class="product-item-details">
							<strong class="product-item-name">
								<a href="{$allData['product_url']}" title="{$allData['product_name']}">{$allData['product_name']}</a>
							</strong>
				
							<div class="product options">
								<span class="toggle" aria-selected="false" aria-expanded="false" tabindex="0">See Details</span>
								<div class="content"><dl class="product options list">{$product_options}</dl></div>
							</div>
							<div class="product-item-pricing">
								<div class="price-container">
									<span class="price-wrapper">
										<span class="price-including-tax" data-label="Incl. Tax">
											<span class="minicart-price"><span class="price">NZ\${$product_price}</span></span>
										</span>
									</span>
								</div>
								<div class="details-qty qty">
									<label class="label" for="cart-item-{$allData['item_id']}-qty">Qty</label>
									<input
										 type="number" min="0" size="4" class="item-qty cart-item-qty"
										id="cart-item-{$allData['item_id']}-qty"
										data-cart-item="{$allData['item_id']}"
										data-item-qty="{$allData['qty']}"
										value="{$allData['qty']}"
										data-cart-item-id="{$allData['product_sku']}">
									<button class="update-cart-item" id="update-cart-item-{$allData['item_id']}" data-cart-item="{$allData['item_id']}" title="Update">
				                        <span>Update</span>
				                    </button>
								</div>
							</div>
							<div class="product actions">
								<div class="primary">
									<a class="action edit" href="{$allData['configure_url']}" title="Edit item">
										<span>Edit</span>
									</a>
								</div>
								<div class="secondary">
									<a href="#" class="action delete" data-cart-item="{$allData['item_id']}" title="Remove">
										<span >Remove</span>
									</a>
								</div>
							</div>
				
						</div>
					</div>
				</li>
			HTML;
			
			
		}
		echo json_encode([
			'summary_count' => $summary_count,
			'subtotalAmount' => $subtotalAmount,
			'items' => $listItem
		]);
	}catch(Exception $e) {
		echo 'Message: ' .$e->getMessage();
		echo $e->getLine();
		echo "Error in this file: " . $e->getFile();
		echo "<pre>";print_r($e->getTrace());
	}