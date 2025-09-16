<?php
/**
 * Arsit_CustomerPassword
 *
 * @category    Arsit
 * @package     Arsit_CustomerPassword
 * @copyright   Copyright (c) 2016 arsit.ru
 * @author      developer@arsit.ru
 */

namespace Arsit\CustomerPassword\Observer\Adminhtml;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Arsit\CustomerPassword\Helper\Data;
use Magento\Framework\Message\ManagerInterface;

class SetPassword implements ObserverInterface
{

    /**
     * Encryption model
     *
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * Logger
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Helper
     *
     * @var \Arsit\CustomerPassword\Helper\Data
     */
    protected $customerPasswordData;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @param EncryptorInterface $encryptor
     * @param CustomerRegistry $customerRegistry
     * @param CustomerRepositoryInterface $customerRepository
     * @param LoggerInterface $logger
     * @param ManagerInterface $messageManager
     * @param Data $customerPasswordData
     */
    public function __construct(
        EncryptorInterface $encryptor,
        CustomerRegistry $customerRegistry,
        CustomerRepositoryInterface $customerRepository,
        LoggerInterface $logger,
        ManagerInterface $messageManager,
        Data $customerPasswordData
    ) {
        $this->encryptor = $encryptor;
        $this->customerRegistry = $customerRegistry;
        $this->customerRepository = $customerRepository;
        $this->logger = $logger;
        $this->customerPasswordData = $customerPasswordData;
        $this->messageManager = $messageManager;
    }

    /**
     * Update customer password
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $password = $this->_fetchPassword($observer->getEvent()->getData('request'));
        if (!empty($password)) {
            $customer = $observer->getEvent()->getData('customer');
            if ($customer instanceof \Magento\Customer\Api\Data\CustomerInterface) {
                $this->logger->debug(
                    'Attempt to change customer passwor for customer '
                    . $customer->getId()
                );

                $customerSecure = $this->customerRegistry->retrieveSecureData($customer->getId());
                $customerSecure->setPasswordHash($this->encryptor->getHash($password, true));
                $this->customerRepository->save($customer);
                $this->messageManager->addSuccess(__('Password has been updated.'));
            } else {
                $this->messageManager->addError(__('Password has not been updated.'));
            }
        }
        
        return $this;
    }

    /**
     * Fetch password from request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return mixed
     */
    public function _fetchPassword(
        \Magento\Framework\App\RequestInterface $request
    ) {
        return $request->getParam($this->customerPasswordData->getElementKey());
    }
}
