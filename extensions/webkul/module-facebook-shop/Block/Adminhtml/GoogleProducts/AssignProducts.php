<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_FacebookShop
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\FacebookShop\Block\Adminhtml\GoogleProducts;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollection;

class AssignProducts extends \Magento\Backend\Block\Template
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'googleproducts/assign_products.phtml';

    /**
     * @var \Magento\Catalog\Block\Adminhtml\Category\Tab\Product
     */
    protected $blockGrid;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

   /**
    * @param \Magento\Backend\Block\Template\Context $context
    * @param \Magento\Framework\Registry $registry
    * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
    * @param ProductCollection $productCollection
    * @param array $data
    */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        ProductCollection $productCollection,
        array $data = []
    ) {
        $this->_registry = $registry;
        $this->_jsonEncoder = $jsonEncoder;
        $this->productCollection = $productCollection;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                \Webkul\FacebookShop\Block\Adminhtml\GoogleProducts\Products::class,
                'facebookshop.googleproducts.grid'
            );
        }
        return $this->blockGrid;
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }
}
