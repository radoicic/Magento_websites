<?php
/**
 * Copyright © 2015 Iksanika. All rights reserved.
 * See IKS-LICENSE.txt for license details.
 */

/**
 * Used in creating options for Yes|No config value selection
 *
 */
namespace Iksanika\Ordermanage\Model;

class Export 
{
    
    const NO_EXPORT     =   0;  // no export, standard view mode
    const ORDERS        =   1;  // standard export with orders
    const ORDERED_ITEMS =   2;  // orders with ordered items
    
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
    }
}
