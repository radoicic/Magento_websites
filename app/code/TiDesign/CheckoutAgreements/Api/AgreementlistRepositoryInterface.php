<?php
/**
 * Copyright © TiDesign All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace TiDesign\CheckoutAgreements\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface AgreementlistRepositoryInterface
{

    /**
     * Save Agreementlist
     * @param \TiDesign\CheckoutAgreements\Api\Data\AgreementlistInterface $agreementlist
     * @return \TiDesign\CheckoutAgreements\Api\Data\AgreementlistInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \TiDesign\CheckoutAgreements\Api\Data\AgreementlistInterface $agreementlist
    );

    /**
     * Retrieve Agreementlist
     * @param string $agreementlistId
     * @return \TiDesign\CheckoutAgreements\Api\Data\AgreementlistInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($agreementlistId);

    /**
     * Retrieve Agreementlist matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \TiDesign\CheckoutAgreements\Api\Data\AgreementlistSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Agreementlist
     * @param \TiDesign\CheckoutAgreements\Api\Data\AgreementlistInterface $agreementlist
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \TiDesign\CheckoutAgreements\Api\Data\AgreementlistInterface $agreementlist
    );

    /**
     * Delete Agreementlist by ID
     * @param string $agreementlistId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($agreementlistId);
}

