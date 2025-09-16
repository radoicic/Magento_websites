<?php
/**
 * Copyright © TiDesign All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace TiDesign\CheckoutAgreements\Api\Data;

interface AgreementlistSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Agreementlist list.
     * @return \TiDesign\CheckoutAgreements\Api\Data\AgreementlistInterface[]
     */
    public function getItems();

    /**
     * Set agreement_id list.
     * @param \TiDesign\CheckoutAgreements\Api\Data\AgreementlistInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

