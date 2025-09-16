/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'underscore',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/action/select-shipping-method'
], function (_, quote, checkoutData, selectShippingMethodAction) {
    'use strict';
    
    return function (target) {
        
        return _.extend(target, {
            
            /**
             * Resolve shipping rates
             * 
             * @param {Object[]} shippingRates
             * @returns {void}
             */
            resolveShippingRates: function (shippingRates) {
                _.each(quote.getSources(), function (source) {
                    var sourceCode = source.source_code;
                    var sourceShippingRates = _.filter(shippingRates, function (shippingRate) {
                        return shippingRate.extension_attributes.source_code === sourceCode;
                    });
                    var shippingMethod = quote.getSourceShippingMethod(sourceCode);
                    if (sourceShippingRates.length === 1 && !shippingMethod) {
                        selectShippingMethodAction(sourceShippingRates[0]);
                        return;
                    }
                    var availableShippingRate = null;
                    if (shippingMethod) {
                        availableShippingRate = _.find(sourceShippingRates, function (shippingRate) {
                            return (shippingRate.carrier_code === shippingMethod.carrier_code) && 
                                (shippingRate.method_code === shippingMethod.method_code);
                        });
                    }
                    var selectedShippingRate = checkoutData.getSelectedSourceShippingRate(sourceCode);
                    if (!availableShippingRate && selectedShippingRate) {
                        availableShippingRate = _.find(sourceShippingRates, function (shippingRate) {
                            return shippingRate.carrier_code + '_' + shippingRate.method_code === selectedShippingRate;
                        });
                    }
                    if (!availableShippingRate && window.checkoutConfig.selectedShippingMethod) {
                        availableShippingRate = _.find(window.checkoutConfig.selectedShippingMethod, function (shippingRate) {
                            return shippingRate.extension_attributes.source_code === sourceCode;
                        });
                    }
                    if (availableShippingRate) {
                        selectShippingMethodAction(availableShippingRate);
                    }
                }, this);
            }
            
        });
        
    };
});