<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Quote\Quote\Item;

/**
 * Quote item source wrapper
 */
class Source extends \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Framework\Source
{
    /**
     * Set source code
     * 
     * @param string|null $sourceCode
     * @return void
     */
    public function setSourceCode(string $sourceCode = null): void
    {
        $object = $this->getObject();
        $object->getExtensionAttributes()->setSourceCode($sourceCode);
        $children = $object->getChildren();
        if (!count($children)) {
            return;
        }
        $parentObject = $object;
        foreach ($children as $childItem) {
            $this->setObject($childItem);
            $this->setSourceCode($sourceCode);
        }
        $this->setObject($parentObject);
    }
}