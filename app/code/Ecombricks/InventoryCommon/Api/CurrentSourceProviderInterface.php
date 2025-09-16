<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Api;

/**
 * Current source provider interface
 */
interface CurrentSourceProviderInterface
{
    /**
     * Set source code
     * 
     * @param string|null $sourceCode
     * @return void
     */
    public function setSourceCode(string $sourceCode = null): void;

    /**
     * Get source code
     * 
     * @return string|null
     */
    public function getSourceCode(): ?string;
}