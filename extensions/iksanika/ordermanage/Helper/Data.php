<?php
/**
 * Iksanika llc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.iksanika.com/products/IKS-LICENSE.txt
 *
 * @category   Iksanika
 * @package    Iksanika_Ordermanage
 * @copyright  Copyright (c) 2015 Iksanika llc. (http://www.iksanika.com)
 * @license    http://www.iksanika.com/products/IKS-LICENSE.txt
 */
namespace Iksanika\Ordermanage\Helper;


/**
 * Catalog data helper
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    public static $columnSettings = array();
     
    protected static $statusList = null;
   
    protected static $columnType = array(
        'id'                    =>  array('type'=>'number'),
        'order_ids'             =>  array('type'=>'checkbox'),
        
        'status'                =>  array('type'=>'options'),
        
//        'name'                  =>  array('type'=>'text', 'title' => 'Name', 'filter_index' => 'group_concat(`sales/order_item`.name SEPARATOR ",")'),
//        'sku'                   =>  array('type'=>'text', 'title' => 'Sku', 'filter_index' => 'group_concat(`sales/order_item`.sku SEPARATOR ",")'),
        'name'                  =>  array('type'=>'text', 'title' => 'Name', 'filter_index' => 'name'),
        'sku'                   =>  array('type'=>'text', 'title' => 'Sku', 'filter_index' => 'sku'),
        
        'billing_full_form'     =>  array('type'=>'text', 'title' => 'Billing Address', 'filter_index' => 'CONCAT_WS(\' \', soa_billing.firstname, soa_billing.lastname, soa_billing.middlename, soa_billing.company, soa_billing.street, soa_billing.city, soa_billing.region, soa_billing.postcode, soa_billing.email, soa_billing.telephone, soa_billing.fax)'),
        'billing_firstname'     =>  array('type'=>'input', 'title' => 'Billing Firstname', 'filter_index' => 'soa_billing.firstname'),
        'billing_middlename'    =>  array('type'=>'input', 'title' => 'Billing Middlename', 'filter_index' => 'soa_billing.middlename'),
        'billing_lastname'      =>  array('type'=>'input', 'title' => 'Billing Lastname', 'filter_index' => 'soa_billing.lastname'),
        'billing_company'       =>  array('type'=>'input', 'title' => 'Billing Company', 'filter_index' => 'soa_billing.company'),
        'billing_street'        =>  array('type'=>'input', 'title' => 'Billing Street', 'filter_index' => 'soa_billing.street'),
        'billing_city'          =>  array('type'=>'input', 'title' => 'Billing City', 'filter_index' => 'soa_billing.city'),
        'billing_region'        =>  array('type'=>'input', 'title' => 'Billing Region', 'filter_index' => 'soa_billing.region'),
        'billing_postcode'      =>  array('type'=>'input', 'title' => 'Billing Postcode', 'filter_index' => 'soa_billing.postcode'),
        'billing_email'         =>  array('type'=>'input', 'title' => 'Billing Email', 'filter_index' => 'soa_billing.email'),
        'billing_telephone'     =>  array('type'=>'input', 'title' => 'Billing Telephone', 'filter_index' => 'soa_billing.telephone'),
        'billing_country'       =>  array('type'=>'country', 'title' => 'Billing Country', 'filter_index' => 'soa_billing.country_id'),
        'billing_fax'           =>  array('type'=>'input', 'title' => 'Billing Fax', 'filter_index' => 'soa_billing.fax'),
        
        'shipping_full_form'    =>  array('type'=>'text', 'title' => 'Shipping Address', 'filter_index' => 'CONCAT_WS(\' \', soa_shippment.firstname, soa_shippment.lastname, soa_shippment.middlename, soa_shippment.company, soa_shippment.street, soa_shippment.city, soa_shippment.region, soa_shippment.postcode, soa_shippment.email, soa_shippment.telephone, soa_shippment.fax)'),
        'shipping_firstname'    =>  array('type'=>'input', 'title' => 'Shipping Firstname', 'filter_index' => 'soa_shippment.firstname'),
        'shipping_middlename'   =>  array('type'=>'input', 'title' => 'Shipping Middlename', 'filter_index' => 'soa_shippment.middlename'),
        'shipping_lastname'     =>  array('type'=>'input', 'title' => 'Shipping Lastname', 'filter_index' => 'soa_shippment.lastname'),
        'shipping_company'      =>  array('type'=>'input', 'title' => 'Shipping Company', 'filter_index' => 'soa_shippment.company'),
        'shipping_street'       =>  array('type'=>'input', 'title' => 'Shipping Street', 'filter_index' => 'soa_shippment.street'),
        'shipping_city'         =>  array('type'=>'input', 'title' => 'Shipping City', 'filter_index' => 'soa_shippment.city'),
        'shipping_region'       =>  array('type'=>'input', 'title' => 'Shipping Region', 'filter_index' => 'soa_shippment.region'),
        'shipping_postcode'     =>  array('type'=>'input', 'title' => 'Shipping Postcode', 'filter_index' => 'soa_shippment.postcode'),
        'shipping_email'        =>  array('type'=>'input', 'title' => 'Shipping Email', 'filter_index' => 'soa_shippment.email'),
        'shipping_telephone'    =>  array('type'=>'input', 'title' => 'Shipping Telephone', 'filter_index' => 'soa_shippment.telephone'),
        'shipping_country'      =>  array('type'=>'country', 'title' => 'Shipping Country', 'filter_index' => 'soa_shippment.country_id'),
        'shipping_fax'          =>  array('type'=>'input', 'title' => 'Shipping Fax', 'filter_index' => 'soa_shippment.fax'),
    );

    public static $_scopeConfig = array();
    
    protected static $imagesWidth = null;
    protected static $imagesHeight = null;
    protected static $imagesScale = null;
    
    protected static $includeProducts = null;
    protected static $showProducts = null;
    
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $productAttrCollection,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Sales\Model\Order\Status $orderStatus,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Helper\Image $catalogImage,


//        \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter,
        \Magento\Framework\Image\Factory $imageFactory,
        \Magento\Framework\Image\AdapterFactory $imageAdapterFactory,


        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Catalog\Model\Product\Media\Config $productMediaConfig,

        \Magento\Framework\Image $frImage
    ) {
        parent::__construct($context);
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->_productAttrCollection = $productAttrCollection;
        $this->_localeCurrency = $localeCurrency;
        $this->_orderStatus = $orderStatus;
        $this->_productFactory = $productFactory;
        $this->_catalogImage = $catalogImage;


//        $this->_adapter = $imageAdapter;
        $this->_imageAdapterFactory = $imageAdapterFactory;
        $this->_imageFactory = $imageFactory;
        $this->_frImage = $frImage;

        $this->_fileSystem = $fileSystem;
        $this->_productMediaConfig = $productMediaConfig;
    }

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
    
    public function getStore()
    {
        return $this->_storeManager->getStore($this->getStoreId());
    }
    
    public static function setScopeConfig($scopeConfig)
    {
        self::$_scopeConfig = $scopeConfig;
    }
    
    public static function getScopeConfig()
    {
        return self::$_scopeConfig;
    }
    

    
    
        
    public function getImageUrl($image_file)
    {
        $url = false;
        $url = $this->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
//        $url = $url.Mage::getBaseUrl('media').'catalog/product'.$image_file;
        $url = $url.'catalog/product'.$image_file;
        return $url;
    }
    
    public function getFileExists($image_file)
    {
        $file_exists = false;
        $file_exists = file_exists('media/catalog/product'. $image_file);
        return $file_exists;
    }
    
    
    
    
    protected static function initSettings()
    {
        
        if(!self::$imagesWidth)
            self::$imagesWidth = self::getScopeConfig()->getValue('iksanika_ordermanage/images/width');
        if(!self::$imagesHeight)
            self::$imagesHeight = self::getScopeConfig()->getValue('iksanika_ordermanage/images/height');
        if(!self::$imagesScale)
            self::$imagesScale = self::getScopeConfig()->getValue('iksanika_ordermanage/images/scale');
        
        if(!self::$includeProducts)
            self::$includeProducts = self::getScopeConfig()->getValue('iksanika_ordermanage/products/includeproducts');
        if(!self::$showProducts)
            self::$showProducts = self::getScopeConfig()->getValue('iksanika_ordermanage/products/showproducts');
        
    }
    
    public function getImage($orderItem)
    {
        self::initSettings();
        
        if($orderItem->getProductType() == 'configurable') 
        {
            $productItem = $orderItem->getProduct();
        }else
        {
            $productItem = $this->_productFactory->create()->load($orderItem->getProductId()); 
        }

        if($productItem && $productItem->getData('small_image') && ($productItem->getData('small_image') != 'no_selection') && ($productItem->getData('small_image') != ""))
        {
            // $filePath = (\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'/catalog/product'.$productItem->getData('small_image');
            $filePath = $this->_fileSystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath($this->_productMediaConfig->getMediaPath($productItem->getData('small_image')));

//            echo $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$productItem->getData('small_image');
//            if(file_exists($_SERVER["DOCUMENT_ROOT"].'/'.$filePath)) {
            if(file_exists($filePath)) {
                $currentImage = $this->_imageFactory->create($filePath);
                $currentImageWidth = 0;
                $currentImageHeight = 0;
                if ($currentImage) {
                    $currentImageWidth = $currentImage->getOriginalWidth();
                    $currentImageHeight = $currentImage->getOriginalHeight();
                }

                if ($currentImageWidth && $currentImageHeight) {
                    $outImagesWidth = (self::$imagesWidth && ($currentImageWidth >= $currentImageHeight)) ? "width='" . self::$imagesWidth . "'" : '';
                    if (self::$imagesScale)
                        $outImagesHeight = (self::$imagesHeight) ? "height='" . self::$imagesHeight . "'" : '';
                    else
                        $outImagesHeight = (self::$imagesHeight && (!self::$imagesWidth || ($currentImageHeight > $currentImageWidth))) ? "height='" . self::$imagesHeight . "'" : '';
                }

                //$this->_frImage(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'catalog/product'.$productItem->getData('small_image');
                //$origWidth = $this->_catalogImage->init($productItem, "product_thumbnail_image")->getOriginalWidth();
                //$origWidth = $this->_catalogImage->init($productItem, "small_image")->getOriginalWidth();
                //$origHeight = $this->_catalogImage->init($productItem, "small_image")->getOriginalHeight();
                //$iWidth = $this->_catalogImage->init($productItem, "small_image")->getWidth();
                //$iHeight = $this->_catalogImage->init($productItem, "small_image")->getHeight();
                //$this->_catalogImage->init($productItem, "small_image")->getOriginalSizeArray();
                //            return '<img src="'.($catImage->init($productItem, "small_image")->resize(self::$imagesWidth)->getUrl()).'" '.$outImagesWidth.' '.$outImagesHeight.' alt="" />';
                //            return '<img src="'.($this->_catalogImage->init($productItem, "small_image")->resize(self::$imagesWidth)->getUrl()).'" '.$outImagesWidth.' '.$outImagesHeight.' alt="" />';

                return '<img src="' . ($this->getImageUrl($productItem->getData('small_image'))) . '" ' . $outImagesWidth . ' ' . $outImagesHeight . ' alt="" />';
            }else
            {
                return '<div style="'.(self::$imagesWidth ? 'width: '.self::$imagesWidth.'px; ' : '').((self::$imagesHeight) ? 'height:'.self::$imagesHeight.'px; ':'').'">[NO IMAGES]</div>';
            }
        }else
        {
            return '<div style="'.(self::$imagesWidth ? 'width: '.self::$imagesWidth.'px; ' : '').((self::$imagesHeight) ? 'height:'.self::$imagesHeight.'px; ':'').'">[NO IMAGES]</div>';
        }
    }

    
    
    
    public static function prepareColumnSettings() 
    {
        $storeSettings = self::$_scopeConfig->getValue('iksanika_ordermanage/columns/showcolumns');
        $tempArr = explode(',', $storeSettings);
        
        foreach($tempArr as $showCol) 
        {
            self::$columnSettings[trim($showCol)] = true;
        }
    }
    
    public static function getColumnSettings()
    {
        if(count(self::$columnSettings) == 0)
        {
            self::prepareColumnSettings();
        }
        return self::$columnSettings;
    }
    
    public static function getColumnForUpdate()
    {
        $fields = array('order_ids');
        
        if(count(self::getColumnSettings()))
        {
            foreach(self::getColumnSettings() as $columnId => $status)
            {
                if(isset(self::$columnType[$columnId]))
                {
                    if(
                        self::$columnType[$columnId]['type'] == 'input' || 
                        self::$columnType[$columnId]['type'] == 'price' || 
                        self::$columnType[$columnId]['type'] == 'number' || 
                        self::$columnType[$columnId]['type'] == 'options' || 
                        self::$columnType[$columnId]['type'] == 'country' || 
                        self::$columnType[$columnId]['type'] == 'date' 
                      )
                    {
                        $fields[] = $columnId;
                    }
                }
            }
        }
        return $fields;
    }
    
    public function colIsVisible($code) 
    {
        $columnSettings = self::getColumnSettings();
        return isset($columnSettings[$code]);
    }
    
    public static function getColumnType()
    {
        return self::$columnType;
    }    
    
    public function getStatusesByState($filter = '')
    {
        // status, label, state, is_default
        if(!self::$statusList)
        {
            $collection = $this->_orderStatus->getCollection()->joinStates();
            self::$statusList = $collection->load();
        }
        if(self::$statusList && !empty(self::$statusList))
        {
            $returnStatusList = array();
            foreach(self::$statusList as $statusItem)
            {
                if($statusItem['state'] == $filter || $filter == '')
                {
                    $returnStatusList[$statusItem['status']] = $statusItem['label'];
                }
            }
            return $returnStatusList;
        }else
            return array();
    }

    /**
     * Get order options
     *
     * @return array
     */
    public function getOrderedItemOptions($orderedItem)
    {
        $result = [];
        if ($options = $orderedItem->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (!empty($options['attributes_info'])) {
                $result = array_merge($options['attributes_info'], $result);
            }
        }
        return $result;
    }

}
