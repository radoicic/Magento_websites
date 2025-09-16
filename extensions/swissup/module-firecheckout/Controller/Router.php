<?php

namespace Swissup\Firecheckout\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{
    const DUMMY_CART_PAGE_PATHS = [
        'firecheckout/cart',
        'firecheckout/cart/index'
    ];

    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Page view helper
     *
     * @var \Swissup\Firecheckout\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $response;

    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Swissup\Firecheckout\Helper\Data $helper
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Swissup\Firecheckout\Helper\Data $helper,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\ResponseInterface $response
    ) {
        $this->actionFactory = $actionFactory;
        $this->helper = $helper;
        $this->url = $url;
        $this->response = $response;
    }

    /**
     * Match firecheckout page
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface|null
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->helper->isFirecheckoutEnabled() || $request->getParam('onepage')) {
            return null;
        }

        $currentPath = trim($request->getPathInfo(), '/');
        if (strpos($currentPath, 'firecheckout') === 0) {
            return $this->redirectNotCheckoutPage($request);
        }

        $firecheckoutPath = $this->helper->getFirecheckoutUrlPath();
        $firecheckoutPaths = [
            $firecheckoutPath,
            $firecheckoutPath . '/index',
            $firecheckoutPath . '/index/index',
        ];

        if (!in_array($currentPath, $firecheckoutPaths)) {
            return null;
        }

        $request->setAlias(
            \Magento\Framework\UrlInterface::REWRITE_REQUEST_PATH_ALIAS,
            $currentPath
        );
        $request->setPathInfo('/firecheckout/index/index');

        return $this->actionFactory->create(
            \Magento\Framework\App\Action\Forward::class
        );
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface|null
     */
    private function redirectNotCheckoutPage($request)
    {
        $currentPath = trim($request->getPathInfo(), '/');
        if (!in_array($currentPath, self::DUMMY_CART_PAGE_PATHS)) {
            return null; // use standard router to prevent recursion
        }
        $request->setDispatched();
        $this->response->setRedirect($this->url->getUrl('checkout/cart/index'));
        return $this->actionFactory->create(\Magento\Framework\App\Action\Redirect::class);
    }
}
