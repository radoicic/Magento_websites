/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'ko',
    'underscore',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/select-shipping-method',
    'Magento_Checkout/js/checkout-data'
], function (ko, _, quote, selectShippingMethodAction, checkoutData) {
    'use strict';
    
    return function (target) {
        
        return target.extend({
            
            defaults: {
                template: 'Ecombricks_InventoryCustomerSourceSelection/checkout/cart/shipping-rates'
            },
            
            sources: ko.observableArray([]),
            
            /**
             * Initialize observable
             * 
             * @returns {Object}
             */
            initObservable: function () {
                this._super();
                this.shippingRates.subscribe(function (shippingRates) {
                    this.sources([]);
                    _.each(shippingRates, function (shippingRate) {
                        var source = quote.getSource(shippingRate.extension_attributes.source_code);
                        if (source && this.sources.indexOf(source) === -1) {
                            this.sources.push(source);
                        }
                    }, this);
                }, this);
                return this;
            },
            
            /**
             * Get source shipping rates
             * 
             * @param {String} sourceCode
             * @returns {Object[]}
             */
            getSourceShippingRates: function (sourceCode) {
                return _.filter(this.shippingRates(), function (shippingRate) {
                    return sourceCode === shippingRate.extension_attributes.source_code;
                });
            },
            
            /**
             * Get source carriers
             * 
             * @param {String} sourceCode
             * @returns {Object[]}
             */
            getSourceCarriers: function (sourceCode) {
                var carriers = [];
                _.each(this.getSourceShippingRates(sourceCode), function (shippingRate) {
                    if (_.find(carriers, function (carrier) { return carrier.code === shippingRate.carrier_code; })) {
                        return;
                    }
                    carriers.push({code: shippingRate.carrier_code, title: shippingRate.carrier_title});
                });
                return carriers;
            },
            
            /**
             * Get source carrier shipping rates
             * 
             * @param {String} sourceCode
             * @param {String} carrierCode
             * @returns {Object[]}
             */
            getSourceCarrierShippingRates: function (sourceCode, carrierCode) {
                return _.filter(this.getSourceShippingRates(sourceCode), function (shippingRate) {
                    return carrierCode === shippingRate.carrier_code;
                });
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
            },
            
            /**
             * Get selected source shipping method
             * 
             * @param {String} sourceCode
             * @returns {String|null}
             */
            getSelectedSourceShippingMethod: function (sourceCode) {
                var shippingMethod = quote.getSourceShippingMethod(sourceCode);
                if (!shippingMethod) {
                    return null;
                }
                return shippingMethod.carrier_code + '_' + shippingMethod.method_code;
            }
        });
    };
});