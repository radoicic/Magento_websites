/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'underscore',
    'Magento_Checkout/js/model/quote'
], function (_, quote) {
    'use strict';
    
    return function (target) {
        
        return target.extend({
            
            /**
             * Get shipping method title
             * 
             * @returns {String}
             */
            getShippingMethodTitle: function () {
                if (!quote.hasJointShippingMethod()) {
                    return '';
                }
                return (_.map(quote.shippingMethods(), function (shippingMethod) {
                    return quote.getSourceName(shippingMethod.extension_attributes.source_code) + ': ' + shippingMethod.carrier_title + ' - ' + shippingMethod.method_title;
                })).join(', ');
            }
            
        });
        
    };
    
});