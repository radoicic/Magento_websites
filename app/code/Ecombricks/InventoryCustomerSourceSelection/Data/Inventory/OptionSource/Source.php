<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Data\Inventory\OptionSource;

/**
 * Source option source
 */
class Source implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Get sources
     * 
     * @var \Ecombricks\InventoryCommon\Model\GetSources
     */
    private $getSources;

    /**
     * Options
     * 
     * @var array
     */
    private $options;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Model\GetSources $getSources
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Model\GetSources $getSources
    )
    {
        $this->getSources = $getSources;
    }
    
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }
        $this->options = [];
        foreach ($this->getSources->execute() as $source) {
            $this->options[] = [
                'label' => $source->getName(),
                'value' => $source->getSourceCode(),
            ];
        }
        return $this->options;
    }
}