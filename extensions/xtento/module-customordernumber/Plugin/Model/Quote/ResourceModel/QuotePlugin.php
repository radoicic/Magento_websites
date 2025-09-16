<?php

/**
 * Product:       Xtento_CustomOrderNumber (2.1.7)
 * ID:            ujUJn/Vth6o24dYqG/9u9lsOOEMJEJPHsCxn5DbLMtc=
 * Packaged:      2018-10-11T07:25:23+00:00
 * Last Modified: 2016-08-12T13:49:23+00:00
 * File:          app/code/Xtento/CustomOrderNumber/Plugin/Model/Quote/ResourceModel/QuotePlugin.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\CustomOrderNumber\Plugin\Model\Quote\ResourceModel;

use Magento\Quote\Model\ResourceModel\Quote;

class QuotePlugin
{
    /**
     * @var \Xtento\CustomOrderNumber\Helper\Generator
     */
    protected $incrementIdGenerator;

    /**
     * QuotePlugin constructor.
     *
     * @param \Xtento\CustomOrderNumber\Helper\Generator $incrementIdGenerator
     */
    public function __construct(
        \Xtento\CustomOrderNumber\Helper\Generator $incrementIdGenerator
    ) {
        $this->incrementIdGenerator = $incrementIdGenerator;
    }

    /**
     * @param Quote $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Model\Quote $quote
     * @return mixed
     */
    public function aroundGetReservedOrderId(Quote $subject, \Closure $proceed, $quote)
    {
        $originalSequence = $proceed($quote);
        // Generate new increment ID
        $incrementId = $this->incrementIdGenerator->generateIncrementId($quote, \Magento\Sales\Model\Order::ENTITY, $originalSequence);
        return $incrementId;
    }
}
