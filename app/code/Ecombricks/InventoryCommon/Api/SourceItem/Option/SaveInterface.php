<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Api\SourceItem\Option;

/**
 * Save source item options interface
 */
interface SaveInterface
{
    /**
     * Execute
     * 
     * @param \Ecombricks\InventoryCommon\Api\Data\SourceItemOptionInterface[] $options
     * @return $this
     */
    public function execute(array $options);
}