<?php
/**
 * Copyright © TiDesign All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace TiDesign\CheckoutAgreements\Model\ResourceModel\Agreementlist;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'agreement_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \TiDesign\CheckoutAgreements\Model\Agreementlist::class,
            \TiDesign\CheckoutAgreements\Model\ResourceModel\Agreementlist::class
        );
    }
}

