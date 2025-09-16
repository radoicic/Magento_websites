<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCommon\Model;

/**
 * Current source provider
 */
class CurrentSourceProvider implements \Ecombricks\InventoryCommon\Api\CurrentSourceProviderInterface
{
    /**
     * Source code
     * 
     * @var string|null
     */
    private $sourceCode;

    /**
     * Set source code
     * 
     * @param string|null $sourceCode
     * @return void
     */
    public function setSourceCode(string $sourceCode = null): void
    {
        $this->sourceCode = $sourceCode;
    }

    /**
     * Get source code
     * 
     * @return string|null
     */
    public function getSourceCode(): ?string
    {
        return $this->sourceCode;
    }
}