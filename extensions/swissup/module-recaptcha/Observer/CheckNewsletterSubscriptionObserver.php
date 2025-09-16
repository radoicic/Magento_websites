<?php

namespace Swissup\Recaptcha\Observer;

class CheckNewsletterSubscriptionObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @param \Magento\Captcha\Helper\Data                      $helper
     * @param \Magento\Framework\App\ActionFlag                 $actionFlag
     * @param \Magento\Framework\Message\ManagerInterface       $messageManager
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     */
    public function __construct(
        \Magento\Captcha\Helper\Data $helper,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Response\RedirectInterface $redirect
    ) {
        $this->helper = $helper;
        $this->actionFlag = $actionFlag;
        $this->messageManager = $messageManager;
        $this->redirect = $redirect;
    }

    /**
     * @param  \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $formId = 'newsletter_subscription';
        $captcha = $this->helper->getCaptcha($formId);
        if ($captcha->isRequired()) {
            /** @var \Magento\Framework\App\Action\Action $controller */
            $controller = $observer->getControllerAction();
            if (!$captcha->isCorrect('')) {
                $this->messageManager->addError(__('Incorrect ReCAPTCHA.'));
                $this->actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
                $controller->getResponse()->setRedirect($this->redirect->getRedirectUrl());
            }
        }
    }
}
