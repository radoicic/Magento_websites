<?php

namespace TiDesign\Videobackground\Controller\Adminhtml\Videolist;

class Index extends \Magento\Backend\App\Action
{

    private $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }


    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
			$resultPage->setActiveMenu('TiDesign_Videobackground::videolist');
            $resultPage->getConfig()->getTitle()->prepend(__("Manage Video Files"));
            return $resultPage;
    }
    /**
     * Check for is allowed.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('TiDesign_Videobackground::videolist');
    }
}
