/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'underscore',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/select-shipping-method',
    'Magento_Checkout/js/checkout-data'
], function (_, quote, selectShippingMethodAction, checkoutData) {
    'use strict';
    
    return function (target) {
        return target.extend({
            defaults: {
                shippingMethodListTemplate: 'Ecombricks_InventoryCustomerSourceSelection/checkout/shipping-address/shipping-method-list',
                shippingMethodItemTemplate: 'Ecombricks_InventoryCustomerSourceSelection/checkout/shipping-address/shipping-method-item'
            },
            
            /**
             * Get sources
             * 
             * @returns {Object[]}
             */
            getSources: function () {
                return quote.getSources();
            },
            
            /**
             * Get source shipping rates
             * 
             * @param {String} sourceCode
             * @returns {unresolved}
             */
            getSourceShippingRates: function (sourceCode) {
                return _.filter(this.rates(), function (shippingRate) {
                    return (sourceCode === shippingRate.extension_attributes.source_code);
                });
            },
            
            /**
             * Get source shipping method
             * 
             * @param {String} sourceCode
             * @returns {String}
             */
            getSourceShippingMethod: function (sourceCode) {
                var sourceShippingMethod = quote.getSourceShippingMethod(sourceCode);
                return sourceShippingMethod ? sourceShippingMethod.carrier_code + '_' + sourceShippingMethod.method_code : null;
            },
            
            /**
             * Select shipping method
             * 
             * @param {Object} shippingMethod
             * @returns {Boolean}
             */
            selectShippingMethod: function (shippingMethod) {
                selectShippingMethodAction(shippingMethod);
                checkoutData.setSelectedSourceShippingRate(shippingMethod.extension_attributes.source_code, shippingMethod.carrier_code + '_' + shippingMethod.method_code);
                return true;
            }
        });
    };
});