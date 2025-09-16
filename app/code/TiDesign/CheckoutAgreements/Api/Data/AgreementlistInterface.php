<?php
/**
 * Copyright © TiDesign All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace TiDesign\CheckoutAgreements\Api\Data;

interface AgreementlistInterface
{

    const CUSTOMER_GROUP = 'customer_group';
    const STORE_ID = 'store_id';
    const NAME = 'name';
    const CONTENT = 'content';
    const AGREEMENT_ID = 'agreement_id';
    const AGREEMENTLIST_ID = 'agreementlist_id';
    const IS_ACTIVE = 'is_active';

    /**
     * Get agreementlist_id
     * @return string|null
     */
    public function getAgreementlistId();

    /**
     * Set agreementlist_id
     * @param string $agreementlistId
     * @return \TiDesign\CheckoutAgreements\Agreementlist\Api\Data\AgreementlistInterface
     */
    public function setAgreementlistId($agreementlistId);

    /**
     * Get agreement_id
     * @return string|null
     */
    public function getAgreementId();

    /**
     * Set agreement_id
     * @param string $agreementId
     * @return \TiDesign\CheckoutAgreements\Agreementlist\Api\Data\AgreementlistInterface
     */
    public function setAgreementId($agreementId);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \TiDesign\CheckoutAgreements\Agreementlist\Api\Data\AgreementlistInterface
     */
    public function setName($name);

    /**
     * Get content
     * @return string|null
     */
    public function getContent();

    /**
     * Set content
     * @param string $content
     * @return \TiDesign\CheckoutAgreements\Agreementlist\Api\Data\AgreementlistInterface
     */
    public function setContent($content);

    /**
     * Get is_active
     * @return string|null
     */
    public function getIsActive();

    /**
     * Set is_active
     * @param string $isActive
     * @return \TiDesign\CheckoutAgreements\Agreementlist\Api\Data\AgreementlistInterface
     */
    public function setIsActive($isActive);

    /**
     * Get store_id
     * @return string|null
     */
    public function getStoreId();

    /**
     * Set store_id
     * @param string $storeId
     * @return \TiDesign\CheckoutAgreements\Agreementlist\Api\Data\AgreementlistInterface
     */
    public function setStoreId($storeId);

    /**
     * Get customer_group
     * @return string|null
     */
    public function getCustomerGroup();

    /**
     * Set customer_group
     * @param string $customerGroup
     * @return \TiDesign\CheckoutAgreements\Agreementlist\Api\Data\AgreementlistInterface
     */
    public function setCustomerGroup($customerGroup);
}

