<?php
/**
 * Copyright © TiDesign All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace TiDesign\CheckoutAgreements\Model;

use Magento\Framework\Model\AbstractModel;
use TiDesign\CheckoutAgreements\Api\Data\AgreementlistInterface;

class Agreementlist extends AbstractModel implements AgreementlistInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\TiDesign\CheckoutAgreements\Model\ResourceModel\Agreementlist::class);
    }

    /**
     * @inheritDoc
     */
    public function getAgreementlistId()
    {
        return $this->getData(self::AGREEMENTLIST_ID);
    }

    /**
     * @inheritDoc
     */
    public function setAgreementlistId($agreementlistId)
    {
        return $this->setData(self::AGREEMENTLIST_ID, $agreementlistId);
    }

    /**
     * @inheritDoc
     */
    public function getAgreementId()
    {
        return $this->getData(self::AGREEMENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setAgreementId($agreementId)
    {
        return $this->setData(self::AGREEMENT_ID, $agreementId);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function getContent()
    {
        return $this->getData(self::CONTENT);
    }

    /**
     * @inheritDoc
     */
    public function setContent($content)
    {
        return $this->setData(self::CONTENT, $content);
    }

    /**
     * @inheritDoc
     */
    public function getIsActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    /**
     * @inheritDoc
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * @inheritDoc
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerGroup()
    {
        return $this->getData(self::CUSTOMER_GROUP);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerGroup($customerGroup)
    {
        return $this->setData(self::CUSTOMER_GROUP, $customerGroup);
    }
}

