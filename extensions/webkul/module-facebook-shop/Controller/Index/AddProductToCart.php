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
namespace Webkul\FacebookShop\Controller\Index;
 
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Checkout\Model\Cart;

class AddProductToCart extends \Magento\Framework\App\Action\Action implements
    \Magento\Framework\App\CsrfAwareActionInterface
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;

   /**
    * @param Context $context
    * @param \Magento\Framework\Data\Form\FormKey $formKey
    * @param PageFactory $resultPageFactory
    * @param \Magento\Catalog\Model\ProductFactory $product
    * @param Cart $cart
    */
    public function __construct(
        Context $context,
        \Magento\Framework\Data\Form\FormKey $formKey,
        PageFactory $resultPageFactory,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        Cart $cart
    ) {
        parent::__construct($context);
        $this->formKey = $formKey;
        $this->resultPageFactory = $resultPageFactory;
        $this->product = $product;
        $this->messageManager = $messageManager;
        $this->cart = $cart;
    }
 
   /**
    * add product to cart
    *
    * @return void
    */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $postParams = $this->getRequest()->getParams();
        if (!empty($postParams['id'])) {
            $productId = $postParams['id'];
            $product = $this->product->create()->load($productId);
          
            if (!empty($product)) {
                $params = [
                    'form_key' => $this->formKey->getFormKey(),
                    'product' =>$productId,
                    'qty'   =>1,
                    'price' => $product->getPrice()
                ];
                try {
                    $this->cart->addProduct($product, $params);
                    $this->cart->save();
                    $this->_redirect("checkout/cart/index");
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    $this->_redirect("checkout/cart/index");
                }
              
            }
        } else {
            $this->_redirect('customer/account/login');
        }
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        return $resultPage;
    }

    /**
     * CSRF validation
     *
     * @param RequestInterface $request
     * @return InvalidRequestException|null
     */
    public function createCsrfValidationException(
        RequestInterface $request
    ): ?InvalidRequestException {
        return null;
    }

   /**
    * post request valudation
    *
    * @param RequestInterface $request
    * @return boolean|null
    */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
