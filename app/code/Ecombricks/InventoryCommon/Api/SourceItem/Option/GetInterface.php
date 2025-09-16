<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Api\SourceItem\Option;

/**
 * Get source item options interface
 */
interface GetInterface
{
    /**
     * Execute
     * 
     * @param array $skus
     * @param array $sourceCodes
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(array $skus, array $sourceCodes): array;
}