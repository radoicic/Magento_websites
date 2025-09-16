<?php
declare(strict_types=1);
namespace TiDesign\CheckoutAgreements\Api;

interface LoglistRepositoryInterface
{

    /**
     * Save Agreementlist
     * @param \TiDesign\CheckoutAgreements\Api\Data\LoglistInterface $loglist
     * @return \TiDesign\CheckoutAgreements\Api\Data\LoglistInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \TiDesign\CheckoutAgreements\Api\Data\LoglistInterface $loglist
    );

    /**
     * Retrieve Agreementlist
     * @param string $loglistId
     * @return \TiDesign\CheckoutAgreements\Api\Data\LoglistInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($loglistId);

    /**
     * Retrieve Agreementlist matching the specified criteria.
     */
    public function getList();

    /**
     * Delete Agreementlist
     * @param \TiDesign\CheckoutAgreements\Api\Data\LoglistInterface $loglist
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \TiDesign\CheckoutAgreements\Api\Data\LoglistInterface $loglist
    );

    /**
     * Delete Agreementlist by ID
     * @param string $loglistId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($loglistId);
}