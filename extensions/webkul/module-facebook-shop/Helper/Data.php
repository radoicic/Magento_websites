<?php
/*
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_FacebookShop
 * @author    Webkul
 * @copyright Copyright(c)Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\FacebookShop\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Customer\Model\Session;
use Magento\Quote\Model\Quote\ItemFactory;
use Magento\Quote\Model\QuoteFactory;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Filesystem;

/**
 * Class CurrentCustomer
 */
class Data extends AbstractHelper
{
    const GOOGLEPRODUTTAXONOMYTXTFILE = 'https://www.google.com/basepages/producttype/taxonomy-with-ids.en-US.txt';
    const FB_PRODUCT_ID = "id";
    const FB_PRODUCT_TITLE = "title";
    const FB_PRODUCT_DESCRIPTION = "description";
    const FB_PRODUCT_AVAILABILITY = "availability";
    const FB_PRODUCT_CONDITION = "condition";
    const FB_PRODUCT_PRICE = "price";
    const FB_PRODUCT_LINK = "link";
    const FB_PRODUCT_IMAGE_LINK = "image_link";
    const FB_PRODUCT_BRAND = "brand";
    const FB_PRODUCT_COLOR = "color";
    const FB_PRODUCT_GENDER = "gender";
    const FB_PRODUCT_ITEMGROUP_ID = "item_group_id";
    const FB_PRODUCT_GPC = "google_product_category";
    const FB_PRODUCT_SIZE = "size";
    const FB_PRODUCT_ADD_IMG_LINK = "additional_image_link";
    const FB_PRODUCT_AGEGROUP = "age_group";
    const FB_PRODUCT_MATERIAL = "material";
    const FB_PRODUCT_PRODUCTTYPE = "product_type";
    const FB_PRODUCT_SALEPRICE = "sale_price";
    const FB_PRODUCT_SALEPRICE_EFFDATE = "sale_price_effective_date";
    const FB_PRODUCT_PATTERN = "pattern";
    const FB_PRODUCT_IVENTORY = "inventory";
    const FB_PRODUCT_SHIPPING_PID = "shipping_profile_id";
    const FB_PRODUCT_RTD = "rich_text_description";
    const FB_PRODUCT_GTIN = "gtin";
    const FB_PRODUCT_MPN = "mpn";
    const FB_PRODUCT_LAUNCHDATE = "launch_date";
    const FB_PRODUCT_EXPIREDATE = "expiration_date";
    const FB_PRODUCT_VISIBILITY = "visibility";
    const FB_PRODUCT_OFFERPRICE = "offer_price";
    const FB_PRODUCT_OFFERPRICEEDATE = "offer_price_effective_date";
    const FB_PRODUCT_RETURNPOLICY = "return_policy_info";
    const FB_PRODUCT_MOILELINK = "mobile_link";
    const FB_PRODUCT_ADDITIONALVAR = "additional_variant_attribute";

    /**
     * @var  \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var Filesystem $fileSystem
     */
    protected $fileSystem;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory
     */
    protected $eavAttribute;

    /**
     * @var \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable
     */
    protected $productConfigurable;

   /**
    * @param Context $context
    * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    * @param FileFactory $fileFactory
    * @param Filesystem $fileSystem
    * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
    * @param \Webkul\FacebookShop\Model\MappingFactory $mappingFactory
    * @param \Webkul\FacebookShop\Model\FeedLogModelFactory $feedLogFactory
    * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory
    * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
    * @param \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $eavAttribute
    * @param \Magento\Store\Model\StoreManagerInterface $storeManager
    * @param \Magento\Catalog\Helper\Image $imageHelper
    * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $productConfigurable
    * @param \Webkul\FacebookShop\Model\Source\ConditionTypes $productCondition
    * @param \Webkul\FacebookShop\Model\Source\Gender $productGender
    * @param \Magento\Catalog\Model\ProductFactory $productModel
    */
    public function __construct(
        Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        FileFactory $fileFactory,
        Filesystem $fileSystem,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Webkul\FacebookShop\Model\MappingFactory $mappingFactory,
        \Webkul\FacebookShop\Model\FeedLogModelFactory $feedLogFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $eavAttribute,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $productConfigurable,
        \Webkul\FacebookShop\Model\Source\ConditionTypes $productCondition,
        \Webkul\FacebookShop\Model\Source\Gender $productGender,
        \Magento\Catalog\Model\ProductFactory $productModel,
        \Magento\Framework\Url $url
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->fileFactory = $fileFactory;
        $this->fileSystem = $fileSystem;
        $this->directoryList = $directoryList;
        $this->mappingFactory = $mappingFactory;
        $this->feedLogFactory = $feedLogFactory;
        $this->productFactory = $productFactory;
        $this->stockRegistry = $stockRegistry;
        $this->eavAttribute = $eavAttribute;
        $this->storeManager = $storeManager;
        $this->imageHelper = $imageHelper;
        $this->productConfigurable = $productConfigurable;
        $this->productCondition = $productCondition;
        $this->productGender = $productGender;
        $this->productModel = $productModel;
        $this->url = $url;
        parent::__construct($context);
    }

    /**
     * return facebookshop configurations
     *
     * @param boolean $field
     * @return void
     */
    public function getConfigValue($groupName, $field = false)
    {
        if ($field) {
            return $this->_scopeConfig
                ->getValue(
                    'facebookshop/'.$groupName.'/'.$field,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
        } else {
            return;
        }
    }

    /**
     * return array of optional fb attributes
     *
     * @return array
     */
    public function getAllFbAttributes()
    {
        $fbAttributeArray = [
            16 => self::FB_PRODUCT_AGEGROUP,
            10 => self::FB_PRODUCT_COLOR,
            17 => self::FB_PRODUCT_MATERIAL,
            12 => self::FB_PRODUCT_ITEMGROUP_ID,
            22 => self::FB_PRODUCT_PATTERN,
            19 => self::FB_PRODUCT_PRODUCTTYPE,
            20 => self::FB_PRODUCT_SALEPRICE,
            21 => self::FB_PRODUCT_SALEPRICE_EFFDATE,
            24 => self::FB_PRODUCT_SHIPPING_PID,
            14 => self::FB_PRODUCT_SIZE,
            25 => self::FB_PRODUCT_RTD,
            23 => self::FB_PRODUCT_IVENTORY,
            26 => self::FB_PRODUCT_GTIN,
            27 => self::FB_PRODUCT_MPN,
            28 => self::FB_PRODUCT_LAUNCHDATE,
            29 => self::FB_PRODUCT_EXPIREDATE,
            30 => self::FB_PRODUCT_VISIBILITY,
            3 => self::FB_PRODUCT_DESCRIPTION
        ];
        return $fbAttributeArray;
    }

    /**
     * generate feed csv
     *
     * @param string $initiatedBy
     * @return void
     */
    public function generateFbFeedCsv($initiatedBy)
    {
        try {
            $fbProductIds = [];
            $assocProductsPrice = [];
            $fbProducts = [];
            $directory = $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA);
            $filePath = 'facebookshop/export/WebkulFbFeedCsv.csv';
            $directory->create('facebookshop/export');
            $stream = $directory->openFile($filePath, 'w+');
            $stream->lock();
            $fbFeedColumns = $this->getSheetHeaders();
            foreach ($fbFeedColumns as $column) {
                $header[] = $column;
            }
            
            $stream->writeCsv($header);
            $productCollection = $this->productFactory->create();
            $productCollection->addAttributeToSelect('*');
            $productCollection->addAttributeToFilter('is_facebook_product', 1);
            $defaultGpc = $this->getConfigValue('facebook_shop_product_configuration', 'default_gpc');
            $defaultBrand = $this->getConfigValue('facebook_shop_product_configuration', 'default_brand');
            $showOutOfStockProducts = $this->getConfigValue('facebook_shop_product_configuration', 'stock_products');
            if (!empty($productCollection) && !empty($productCollection->getSize())) {
                foreach ($productCollection as $product) {
                    $productPrice = $product->getPrice();
                    $baseUrl = $this->storeManager->getStore()->getBaseUrl();
                    $checkoutUrl =  $baseUrl.'facebookshop/index/addproducttocart/id/'.$product->getId();
                    if ($product->getTypeId() == 'configurable') {
                        continue;
                    }
                    if ($product->getTypeId() == 'bundle') {
                        $finalPrice   = $product->getPriceInfo()->getPrice('final_price')->getValue();
                        $minimumPrice = $product->getPriceInfo()->getPrice('final_price')->
                        getMinimalPrice()->getValue();
                        $maximumPrice = $product->getPriceInfo()->getPrice('final_price')->
                        getMaximalPrice()->getValue();
                        $productPrice = $minimumPrice;
                        $checkoutUrl =  $product->getProductUrl();
                    }
                    if ($product->getTypeId() == 'grouped') {
                        $associatedProducts =  $product->getTypeInstance()->getAssociatedProducts($product);
                        foreach ($associatedProducts as $assoc) {
                            array_push($assocProductsPrice, $assoc->getPrice());
                        }
                        $productPrice = min($assocProductsPrice);
                        $checkoutUrl =  $product->getProductUrl();
                    }
                    array_push($fbProductIds, $product->getId());
                    $checkConfigurable = $this->productConfigurable->getParentIdsByChild($product->getId());
                    $stockData = $this->stockRegistry->getStockItem($product->getId());
                    if (empty($showOutOfStockProducts) && empty($stockData->getIsInStock())) {
                        continue;
                    }
                     $stock = 'out of stock';
                    if ($stockData->getIsInStock()) {
                        $stock = 'in stock';
                    }
                     $additionalProductString = "";
                     $productImages = $this->getProductImages($product->getId());
                    if (count($productImages) > 1) {
                        $additionalProductString = implode(',', $productImages);
                    }
                    $image = $this->imageHelper->init($product, 'product_page_image_large')
                    ->setImageFile($product->getImage())
                    ->getUrl();
                    $skuPrefix = $this->getConfigValue('facebook_shop_product_configuration', 'sku_prefix');
                     $facebookProduct[self::FB_PRODUCT_ID]      = $skuPrefix.$product->getId();
                     $facebookProduct[self::FB_PRODUCT_TITLE]   = $product->getName();
                     $facebookProduct[self::FB_PRODUCT_DESCRIPTION] = $this->getMappedAttributeValue(
                         self::FB_PRODUCT_DESCRIPTION,
                         $product
                     ) ?? $product->getDescription();
                     $facebookProduct[self::FB_PRODUCT_AVAILABILITY] = $stock;
                     $productCondition = "new";
                    if (!empty($product->getProductCondition())) {
                        $productCondition = $product->getProductCondition();
                    }
                     $facebookProduct[self::FB_PRODUCT_CONDITION] = $productCondition;
                     $currencyCode = $this->storeManager->getStore()->getCurrentCurrency()->getCode();
                     $facebookProduct[self::FB_PRODUCT_PRICE] = $productPrice." ".$currencyCode;
                    if (!empty($checkConfigurable[0])) {
                        $parentId = $checkConfigurable[0];
                        $productLink = $this->loadParentUrl($parentId);
                    } else {
                        $productLink = $product->getProductUrl();
                    }
                       
                    if (empty($product->getRedirectToProduct())) {
                        $productLink = $checkoutUrl;
                    }
                     $facebookProduct[self::FB_PRODUCT_LINK] = $productLink;
                     $facebookProduct[self::FB_PRODUCT_IMAGE_LINK] = $image;
                     $brand = $defaultBrand;
                    if (!empty($product->getFbProductBrand())) {
                        $brand = $product->getFbProductBrand();
                    }
                     $facebookProduct[self::FB_PRODUCT_BRAND] =  $brand;
                     $facebookProduct[self::FB_PRODUCT_COLOR] = $this->getMappedAttributeValue(
                         self::FB_PRODUCT_COLOR,
                         $product
                     ) ?? "";
                     $gender = '';
                    if (!empty($product->getFbProductGender())) {
                        $gender = $product->getFbProductGender();
                    }
                     $facebookProduct[self::FB_PRODUCT_GENDER] = $gender;
                     $itemGroupId = "";
                     $facebookProduct['is_configurable'] = 0;
                    if (!empty($checkConfigurable[0]) && $product->getTypeId() != 'configurable') {
                        $parentId = $checkConfigurable[0];
                        $facebookProduct['is_configurable'] = 1;
                        $itemGroupId = 'Fb_config_'.$parentId;
                    }
                    $facebookProduct[self::FB_PRODUCT_ITEMGROUP_ID] = $itemGroupId;
                    $gpc = $defaultGpc;
                    if (!empty($product->getGoogleProductCategory())) {
                        $gpc = $product->getGoogleProductCategory();
                    }
                    $facebookProduct[self::FB_PRODUCT_GPC] = $gpc;
                     $facebookProduct[self::FB_PRODUCT_SIZE] = $this->getMappedAttributeValue(
                         self::FB_PRODUCT_SIZE,
                         $product
                     ) ?? "";
                     $facebookProduct[self::FB_PRODUCT_ADD_IMG_LINK] = $this->getMappedAttributeValue(
                         self::FB_PRODUCT_ADD_IMG_LINK,
                         $product
                     ) ?? $additionalProductString;
                     $facebookProduct[self::FB_PRODUCT_AGEGROUP] = $this->getMappedAttributeValue(
                         self::FB_PRODUCT_AGEGROUP,
                         $product
                     ) ?? "";
                     $facebookProduct[self::FB_PRODUCT_MATERIAL] = $this->getMappedAttributeValue(
                         self::FB_PRODUCT_MATERIAL,
                         $product
                     ) ?? "";
                     $facebookProduct[self::FB_PRODUCT_PRODUCTTYPE] = $this->getMappedAttributeValue(
                         self::FB_PRODUCT_PRODUCTTYPE,
                         $product
                     ) ?? "";
                   
                    $salePrice = $this->getMappedAttributeValue(
                        self::FB_PRODUCT_SALEPRICE,
                        $product
                    );
                    if (!empty($product->getSpecialPrice())) {
                        $salePrice = $product->getSpecialPrice();
                    }
                    if (!empty($salePrice)) {
                        $salePrice = $salePrice." ".$currencyCode;
                    }
                     $facebookProduct[self::FB_PRODUCT_SALEPRICE] = $salePrice ?? "0 ".$currencyCode;
                     $dateFbSpecial = "";
                    if (!empty($product->getSpecialFromDate()) && !empty($product->getSpecialToDate())) {
                        $fromDateArr = explode(" ", $product->getSpecialFromDate());
                        $fromTime = date("H:i", strtotime($fromDateArr[1]));
                        $toDateArr = explode(" ", $product->getSpecialToDate());
                        $toTime = date("H:i", strtotime($toDateArr[1]));
                        $dateFbSpecial = $fromDateArr[0].'T0:00-'.$fromTime.'/'.$toDateArr[0].'TO:00-'.$toTime;
                    }
                     $facebookProduct[self::FB_PRODUCT_SALEPRICE_EFFDATE] = $this->getMappedAttributeValue(
                         self::FB_PRODUCT_SALEPRICE_EFFDATE,
                         $product
                     ) ?? $dateFbSpecial;
                     $facebookProduct[self::FB_PRODUCT_PATTERN] = $this->getMappedAttributeValue(
                         self::FB_PRODUCT_PATTERN,
                         $product
                     ) ?? "";
                     $facebookProduct[self::FB_PRODUCT_IVENTORY] = $this->getMappedAttributeValue(
                         self::FB_PRODUCT_IVENTORY,
                         $product
                     ) ?? "";
                     $facebookProduct[self::FB_PRODUCT_SHIPPING_PID] = $this->getMappedAttributeValue(
                         self::FB_PRODUCT_SHIPPING_PID,
                         $product
                     ) ?? "";
                     $facebookProduct[self::FB_PRODUCT_RTD] = $this->getMappedAttributeValue(
                         self::FB_PRODUCT_RTD,
                         $product
                     ) ?? "";
                     $facebookProduct[self::FB_PRODUCT_GTIN] = $this->getMappedAttributeValue(
                         self::FB_PRODUCT_GTIN,
                         $product
                     ) ?? "";
                     $facebookProduct[self::FB_PRODUCT_MPN] = $this->getMappedAttributeValue(
                         self::FB_PRODUCT_MPN,
                         $product
                     ) ?? "";
                     $facebookProduct[self::FB_PRODUCT_LAUNCHDATE] = $this->getMappedAttributeValue(
                         self::FB_PRODUCT_LAUNCHDATE,
                         $product
                     ) ?? "";
                     $facebookProduct[self::FB_PRODUCT_EXPIREDATE] = $this->getMappedAttributeValue(
                         self::FB_PRODUCT_EXPIREDATE,
                         $product
                     ) ?? "";
                     $facebookProduct[self::FB_PRODUCT_VISIBILITY] = $this->getMappedAttributeValue(
                         self::FB_PRODUCT_VISIBILITY,
                         $product
                     ) ?? "";
                    $productString = '';
                    $result = $this->validateProductData($facebookProduct);
                    if (!empty($result['critical'])) {
                        $message = __('Critical Errors: ').$result['msg']." Invalid Product Id : ".$result['productId'];
                        $status = 'Failed';
                        $productString = __('No Products Added, Csv Restored');
                        $this->generateCsvLogs($initiatedBy, $message, $status, $productString);
                        return $result;
                    }
                     $fbProducts[] = $facebookProduct;
                    
                }
                foreach ($fbProducts as $item) {
                    $itemData = [];
                    foreach ($item as $key => $val) {
                        if ($key == 'is_configurable') {
                            continue;
                        }
                        $itemData[] = $item[$key];
                    }
                    $stream->writeCsv($itemData);
                }
                    
                    $content = [];
                    $content['type'] = 'filename';
                    $content['value'] = $filePath;
                    $content['rm'] = '1';
                if (!empty($result['error'])) {
                    $message = __('Warnings for generated feed :').$result['msg'];
                } else {
                    $message = __("Facebook feed Csv generated successfully ");
                }
                    $status = __('Success');
                    $this->generateCsvLogs($initiatedBy, $message, $status, $fbProductIds);
                    $csvfilename = 'WebkulFbFeedCsv.csv';
            } else {
                $result['warning'] = 1;
                $result['message'] = __('No Available Products found to Update on Facebook');
                return $result;
            }
        } catch (\Exception $e) {
            $result['error'] = 1;
            $result['message'] = $e->getMessage();
            return $result;
        }
    }

    /**
     * add record to feed log
     *
     * @param string $initiatedBy
     * @param string $message
     * @param string $status
     * @param string $fbProductIds
     * @return void
     */
    public function generateCsvLogs($initiatedBy, $message, $status, $fbProductIds)
    {
        if (!empty($fbProductIds) && is_array($fbProductIds)) {
            $productString =  implode(',', $fbProductIds);
        } else {
            $productString = $fbProductIds;
        }
        $feedLogModel = $this->feedLogFactory->create();
        $feedLogModel->setInitiatedBy($initiatedBy);
        $feedLogModel->setMessage($message);
        $feedLogModel->setStatus($status);
        $feedLogModel->setCsvPath('pub/media/facebookshop/export/WebkulFbFeedCsv.csv');
        $feedLogModel->setAddedProducts($productString);
        $feedLogModel->save();
    }

    /**
     * return $product images
     *
     * @param int $productId
     * @return void
     */
    public function getProductImages($productId)
    {
        $imagesArray = [];
        $product = $this->productModel->create()->load($productId);
        $images = $product->getMediaGalleryImages();
        if (!empty($images)) {
            foreach ($images as $data) {
                array_push($imagesArray, $data->getUrl());
            }
        }
        return $imagesArray;
    }

    /**
     * validate data
     *
     * @param array $rowData
     * @return array
     */
    public function validateProductData($rowData)
    {
        $result = [];
       
        if (empty($rowData[self::FB_PRODUCT_ID])) {
            
            $result['error'] = 1;
            $result['critical'] = 1;
            $result['productId'] = $rowData[self::FB_PRODUCT_ID];
            $result['msg'] = __('Missing field values for column %1.', self::FB_PRODUCT_ID);
            return $result;
        }
        $validateResult = $this->validateText($rowData[self::FB_PRODUCT_TITLE], 150, self::FB_PRODUCT_TITLE);
        if (!empty($validateResult['error'])) {
            $result['error'] = 1;
            $result['critical'] = 1;
            $result['productId'] = $rowData[self::FB_PRODUCT_ID];
            $result['msg'] = __($validateResult['msg']);
            return $result;
        }
        $validateResult = $this->validateText(
            $rowData[self::FB_PRODUCT_DESCRIPTION],
            5000,
            self::FB_PRODUCT_DESCRIPTION
        );
        if (!empty($validateResult['error'])) {
            $result['error'] = 1;
            $result['critical'] = 1;
            $result['productId'] = $rowData[self::FB_PRODUCT_ID];
            $result['msg'] = __($validateResult['msg']);
            return $result;
        }
        $validateResult = $this->validateAvailability($rowData[self::FB_PRODUCT_AVAILABILITY]);
        if (!empty($validateResult['error'])) {
            $result['error'] = 1;
            $result['critical'] = 1;
            $result['productId'] = $rowData[self::FB_PRODUCT_ID];
            $result['msg'] = __($validateResult['msg']);
            return $result;
        }
        $validateResult = $this->validateCondition($rowData[self::FB_PRODUCT_CONDITION]);
        if (!empty($validateResult['error'])) {
            $result['error'] = 1;
            $result['critical'] = 1;
            $result['productId'] = $rowData[self::FB_PRODUCT_ID];
            $result['msg'] = __($validateResult['msg']);
            return $result;
        }
        if (empty($rowData[self::FB_PRODUCT_PRICE])) {
            $result['error'] = 1;
            $result['critical'] = 1;
            $result['productId'] = $rowData[self::FB_PRODUCT_ID];
            $result['msg'] = __('Missing field values for column %1.', self::FB_PRODUCT_PRICE);
            return $result;
        }
        $validateResult = $this->validateLinks($rowData[self::FB_PRODUCT_LINK], self::FB_PRODUCT_LINK);
        if (!empty($validateResult['error'])) {
             $result['error'] = 1;
             $result['critical'] = 1;
             $result['productId'] = $rowData[self::FB_PRODUCT_ID];
             $result['msg'] = __($validateResult['msg']);
             return $result;
        }
        $validateResult = $this->validateLinks($rowData[self::FB_PRODUCT_IMAGE_LINK], self::FB_PRODUCT_IMAGE_LINK);
        if (!empty($validateResult['error'])) {
             $result['error'] = 1;
             $result['critical'] = 1;
             $result['productId'] = $rowData[self::FB_PRODUCT_ID];
             $result['msg'] = __($validateResult['msg']);
             return $result;
        }
        if (empty($rowData[self::FB_PRODUCT_BRAND])) {
            $result['error'] = 1;
            $result['critical'] = 1;
            $result['productId'] = $rowData[self::FB_PRODUCT_ID];
            $result['msg'] = __('Missing field values for column %1.', self::FB_PRODUCT_BRAND);
            return $result;
        }
        $validateResult = $this->validateGoogleProductCategory($rowData[self::FB_PRODUCT_GPC], 250);
        if (!empty($validateResult['error'])) {
             $result['error'] = 1;
             $result['msg'] = __($validateResult['msg']);
             return $result;
        }
        $validateResult = $this->validateGender($rowData[self::FB_PRODUCT_GENDER]);
        if (!empty($validateResult['error'])) {
             $result['error'] = 1;
             $result['msg'] = __($validateResult['msg']);
             return $result;
        }
        $validateResult = $this->validatePrices($rowData[self::FB_PRODUCT_SALEPRICE], self::FB_PRODUCT_SALEPRICE);
        if (!empty($validateResult['error'])) {
             $result['error'] = 1;
             $result['msg'] = __($validateResult['msg']);
             return $result;
        }
        $validateResult = $this->validateDates($rowData[self::FB_PRODUCT_EXPIREDATE], self::FB_PRODUCT_EXPIREDATE);
        if (!empty($validateResult['error'])) {
             $result['error'] = 1;
             $result['msg'] = __($validateResult['msg']);
             return $result;
        }
        $validateResult = $this->validateDates($rowData[self::FB_PRODUCT_LAUNCHDATE], self::FB_PRODUCT_LAUNCHDATE);
        if (!empty($validateResult['error'])) {
             $result['error'] = 1;
             $result['msg'] = __($validateResult['msg']);
             return $result;
        }
        $validateResult = $this->validateVisibility($rowData[self::FB_PRODUCT_VISIBILITY]);
        if (!empty($validateResult['error'])) {
             $result['error'] = 1;
             $result['msg'] = __($validateResult['msg']);
             return $result;
        }
        if (!empty($rowData['is_configurable'])) {
            $validateResult = $this->validateColor($rowData[self::FB_PRODUCT_COLOR], 100, 1);
            if (!empty($validateResult['error'])) {
                 $result['error'] = 1;
                 $result['msg'] = __($validateResult['msg']);
                 return $result;
            }
            $validateResult = $this->validateItemGroupId($rowData[self::FB_PRODUCT_ITEMGROUP_ID], 1);
            if (!empty($validateResult['error'])) {
                 $result['error'] = 1;
                 $result['critical'] = 1;
                 $result['msg'] = __($validateResult['msg']);
                 return $result;
            }
            $validateResult = $this->validateSize($rowData[self::FB_PRODUCT_SIZE], 1);
            if (!empty($validateResult['error'])) {
                 $result['error'] = 1;
                 $result['msg'] = __($validateResult['msg']);
                 return $result;
            }
        }

        return $result;
    }

    /**
     * limit chk
     *
     * @param string $type
     * @param string $value
     * @param int $limit
     * @return void
     */
    public function attributeLimitCheck($type, $value, $limit)
    {
        if (strlen($value) > $limit) {
            $result['error'] = 1;
            $result['msg'] = __('Max character limit for column %1 is %2.', $type, $limit);
            return $result;
        }
    }

    /**
     * validate product price
     *
     * @param double $price
     * @param string $columnName
     * @return void
     */
    public function validatePrices($price, $columnName)
    {
        $result = [];
        if (!empty($price)) {
            $unitPrice = explode(" ", $price);
            if (!empty($unitPrice[0])) {
                $pattern = '/^\d+(\.\d{2})?$/';
                $rpPrice = round($unitPrice[0], 2);
                if (preg_match($pattern, $rpPrice) == 0) {
                    $result['error'] = 1;
                    $result['msg'] = __('Property %1 is incorrectly formatted.', $columnName);
                    return $result;
                }
            }
           
        }
        return $result;
    }
    /**
     * validate dates
     *
     * @param string $date
     * @param string $columnName
     * @return void
     */
    public function validateDates($date, $columnName)
    {
        $result = [];
        if (!empty($date)) {
            $dateArray =   explode(" ", $date);
            if (!empty($dateArray[0])) {
                if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $dateArray[0]) == 0) {
                    $result['error'] = 1;
                    $result['msg'] = __('Property %1 is incorrectly formatted.', $columnName);
                    return $result;
                }
            }
        }
        
        return $result;
    }
    /**
     * validate item group Id
     *
     * @param string $itemGroup
     * @param int $isConfig
     * @return void
     */
    public function validateItemGroupId($itemGroup, $isConfig)
    {
        $result = [];
        if ($isConfig) {
            if (empty($itemGroup)) {
                $result['error'] = 1;
                $result['msg'] = __('Missing field values for column %1. 
                This is a required field for variants of products', self::FB_PRODUCT_ITEMGROUP_ID);
                return $result;
            }
        }
        return $result;
    }

    /**
     * validate color for variants
     *
     * @param string $color
     * @param int $limit
     * @param string $isConfig
     * @return void
     */
    public function validateColor($color, $limit, $isConfig)
    {
        $result = [];
        if ($isConfig) {
            if (empty($color)) {
                $result['error'] = 1;
                $result['msg'] = __('Missing field values for column %1.', self::FB_PRODUCT_COLOR);
                return $result;
            }
            if (!ctype_alpha($color)) {
                $result['error'] = 1;
                $result['msg'] = __('Only Alphabets values allowed for column %1.', self::FB_PRODUCT_COLOR);
                return $result;
            }
            if (strlen($color) > $limit) {
                $result['error'] = 1;
                $result['msg'] = __('Max character limit for column %1 is %2.', self::FB_PRODUCT_COLOR, $limit);
                return $result;
            }
        }
        return $result;
    }
     /**
      * validate size for variants
      *
      * @param string $color
      * @param int $limit
      * @param string $isConfig
      * @return void
      */
    public function validateSize($size, $isConfig)
    {
        $result = [];
        if ($isConfig) {
            if (empty($size)) {
                $result['error'] = 1;
                $result['msg'] = __('Missing field values for column %1.', self::FB_PRODUCT_SIZE);
                return $result;
            }
        }
        return $result;
    }
    /**
     * validate gpc
     *
     * @param string $gpc
     * @return void
     */
    public function validateGoogleProductCategory($gpc, $limit)
    {
        $result = [];
        if (empty($gpc)) {
            $result['error'] = 1;
            $result['msg'] = __('Missing field values for column %1.', self::FB_PRODUCT_GPC);
            return $result;
        }
        if (strlen($gpc) > $limit) {
            $result['error'] = 1;
            $result['msg'] = __('Max character limit for column %1 is %2.', self::FB_PRODUCT_GPC, $limit);
            return $result;
        }
        return $result;
        ;
    }

    /**
     * validates product title
     *
     * @param string $title
     * @param int $limit
     * @return array
     */
    public function validateText($title, $limit, $fieldName)
    {
        $result = [];
        if (empty($title)) {
            $result['error'] = 1;
            $result['msg'] = __('Missing field values for column %1.', $fieldName);
            return $result;
        }
        if (strlen($title) > $limit) {
            $result['error'] = 1;
            $result['msg'] = __('Max character limit for column %1 is %2.', $fieldName, $limit);
            return $result;
        }
        return $result;
    }

    /**
     * validates product title
     *
     * @param string $title
     * @param int $limit
     * @return array
     */
    public function validateGender($gender)
    {
        $result = [];
        $masterValues = [];
        $options = $this->productGender->getAllOptions();
        foreach ($options as $master) {
            array_push($masterValues, $master['value']);
        }
        if (!empty($gender) && !in_array($gender, $masterValues)) {
            $result['error'] = 1;
            $result['msg'] = __('Invalid column value %1 for %2', self::FB_PRODUCT_GENDER, $gender);
            return $result;
        }
        return $result;
        ;
    }

   /**
    * validate links
    *
    * @param string $link
    * @return array
    */
    public function validateLinks($link, $column)
    {
        $result = [];
        $link = filter_var($link, FILTER_SANITIZE_URL);
        if (empty($link)) {
            $result['error'] = 1;
            $result['msg'] = __('Missing field values for column %1.', $column);
            return $result;
        }
        if (filter_var($link, FILTER_VALIDATE_URL)) {
            $result['error'] = false;
            $result['msg'] = "";
            return true;
        }
        $result['error'] = true;
        $result['msg'] = __('Missing or invalid URL links');
        return $result;
    }

    /**
     * validte product availability for facebook
     *
     * @param string $availability
     * @return array
     */
    public function validateAvailability($availability)
    {
        $result = [];
        $masterValues = ['in stock','available for order','preorder','out of stock','discontinued'];
        if (empty($availability)) {
            $result['error'] = 1;
            $result['msg'] = __('Missing field values for column %1.', self::FB_PRODUCT_AVAILABILITY);
            return $result;
        }
        if (!in_array($availability, $masterValues)) {
            $result['error'] = 1;
            $result['msg'] = __('Invalid column value %1 for %2', self::FB_PRODUCT_AVAILABILITY, $availability);
            return $result;
        }
        return $result;
    }

    /**
     * validate visibility
     *
     * @param string $value
     * @return void
     */
    public function validateVisibility($value)
    {
        $result = [];
        $masterValues = ['published','staging','hidden','whitelist_only'];
        if (!empty($value) && !in_array($value, $masterValues)) {
            $result['error'] = 1;
            $result['msg'] = __('Invalid column value %1 for %2', self::FB_PRODUCT_VISIBILITY, $value);
            return $result;
        }
        return $result;
    }

    /**
     * validate age group
     *
     * @param string  $value
     * @return void
     */
    public function validateAgeGroup($value)
    {
        $result = [];
        $masterValues = ['newborn','infant','toddler','kids','adult'];
        if (!empty($value) && !in_array($value, $masterValues)) {
            $result['error'] = 1;
            $result['msg'] = __('Invalid column value %1 for %2', self::FB_PRODUCT_AGEGROUP, $value);
            return $result;
        }
        return $result;
    }
    /**
     * validte product condition for facebook
     *
     * @param string $condition
     * @return array
     */
    public function validateCondition($condition)
    {
        $result = [];
        $masterValues = [];
        $options = $this->productCondition->getAllOptions();
        foreach ($options as $master) {
            array_push($masterValues, $master['value']);
        }
        if (empty($condition)) {
            $result['error'] = 1;
            $result['msg'] = __('Missing field values for column %1.', self::FB_PRODUCT_AVAILABILITY);
            return $result;
        }
        if (!in_array($condition, $masterValues)) {
            $result['error'] = 1;
            $result['msg'] = __('Invalid column value %1 for %2', self::FB_PRODUCT_AVAILABILITY, $availability);
            return $result;
        }
        return $result;
    }

    /**
     * return mapped attribute values
     *
     * @param string $fbAttributeCode
     * @param array $product
     * @return string
     */
    public function getMappedAttributeValue($fbAttributeCode, $product)
    {
        try {
            $attributeValue = null;
            $attributeCode = '';
            $fbAttributeId = $this->getFbAttributeIdFromCode($fbAttributeCode);
            $mappingCollection =  $this->mappingFactory->create()->getCollection();
            $mappingCollection->addFieldToFilter('fb_attribute_id', ['eq' => $fbAttributeId]);
            if (!empty($mappingCollection->getSize())) {
                
                foreach ($mappingCollection as $mapping) {
                    $productAttributeId =  $mapping->getProductAttributeId();
                    $eavModel = $this->eavAttribute->create()->load($productAttributeId);
                    if (!empty($eavModel)) {
                            $attributeCode =  $eavModel->getAttributeCode();
                        if ($eavModel->getFrontendInput() == 'select') {
                            return $product->getAttributeText($attributeCode);
                        }
                    }
                    return $product->getData($attributeCode);
                }
            }
            return $attributeValue;
        } catch (\Exception $e) {
            return $attributeValue;
        }
    }

    /**
     * return id from fb attribute code
     *
     * @param string $code
     * @return int
     */
    public function getFbAttributeIdFromCode($code)
    {
        $attributeId = 0;
        if (!empty($code)) {
            $sheetHeaders =  $this->getSheetHeaders();
            if (in_array($code, $sheetHeaders)) {
                $attributeId = array_search($code, $sheetHeaders);
            }
        }
        return $attributeId;
    }

    /**
     * return sheet headers
     *
     * @return array
     */
    public function getSheetHeaders()
    {
        $sheetHeaders = [
            1 => self::FB_PRODUCT_ID,
            2 => self::FB_PRODUCT_TITLE,
            3 => self::FB_PRODUCT_DESCRIPTION,
            4 => self::FB_PRODUCT_AVAILABILITY,
            5 => self::FB_PRODUCT_CONDITION,
            6 => self::FB_PRODUCT_PRICE,
            7 => self::FB_PRODUCT_LINK,
            8 => self::FB_PRODUCT_IMAGE_LINK,
            9 => self::FB_PRODUCT_BRAND,
            10 => self::FB_PRODUCT_COLOR,
            11 => self::FB_PRODUCT_GENDER,
            12 => self::FB_PRODUCT_ITEMGROUP_ID,
            13 => self::FB_PRODUCT_GPC,
            14 => self::FB_PRODUCT_SIZE,
            15 => self::FB_PRODUCT_ADD_IMG_LINK,
            16 => self::FB_PRODUCT_AGEGROUP,
            17 => self::FB_PRODUCT_MATERIAL,
            19 => self::FB_PRODUCT_PRODUCTTYPE,
            20 => self::FB_PRODUCT_SALEPRICE,
            21 => self::FB_PRODUCT_SALEPRICE_EFFDATE,
            22 => self::FB_PRODUCT_PATTERN,
            23 => self::FB_PRODUCT_IVENTORY,
            24 => self::FB_PRODUCT_SHIPPING_PID,
            25 => self::FB_PRODUCT_RTD,
            26 => self::FB_PRODUCT_GTIN,
            27 => self::FB_PRODUCT_MPN,
            28 => self::FB_PRODUCT_LAUNCHDATE,
            29 => self::FB_PRODUCT_EXPIREDATE,
            30 => self::FB_PRODUCT_VISIBILITY
        ];
        return $sheetHeaders;
    }

    /**
     * load parent url
     *
     * @param int $productId
     * @return void
     */
    public function loadParentUrl($productId)
    {
        if (!empty($productId)) {
            $product =    $this->productModel->create()->load($productId);
            if (!empty($product->getId())) {
                return $product->getProductUrl();
            }
        }
        return "";
    }
}
