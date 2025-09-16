/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'Magento_Checkout/js/model/quote',
], function (quote) {
    'use strict';
    
    return function () {
        return function (shippingMethod) {
            if (shippingMethod) {
                quote.setSourceShippingMethod(shippingMethod.extension_attributes.source_code, shippingMethod);
            }
        };
    };
});