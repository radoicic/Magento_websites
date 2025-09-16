/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'underscore',
    'Magento_Customer/js/customer-data'
], function (_, storage) {
    'use strict';
    
    var cacheKey = 'checkout-data',
    
    /**
     * Save data
     * 
     * @param {Object} data
     * @returns {void}
     */
    saveData = function (data) {
        storage.set(cacheKey, data);
    },
    
    /**
     * Get data
     * 
     * @returns {Object}
     */
    getData = function () {
        return storage.get(cacheKey)();
    };
    
    return function (target) {
        
        return _.extend(target, {
            
            /**
             * Get prepared data
             * 
             * @returns {Object}
             */
            getPreparedData: function () {
                this.getSelectedShippingRate();
                var data = getData();
                if (!('selectedSourceShippingRates' in data)) {
                    data.selectedSourceShippingRates = {};
                }
                return data;
            },
            
            /**
             * Set selected source shipping rate
             * 
             * @param {String} sourceCode
             * @param {String} shippingRate
             * @returns {Object}
             */
            setSelectedSourceShippingRate: function (sourceCode, shippingRate) {
                var data = this.getPreparedData();
                data.selectedSourceShippingRates[sourceCode] = shippingRate;
                saveData(data);
                return this;
            },
            
            /**
             * Get selected source shipping rate
             * 
             * @param {String} sourceCode
             * @returns {String}
             */
            getSelectedSourceShippingRate: function (sourceCode) {
                var data = this.getPreparedData();
                return ('sourceCode' in data.selectedSourceShippingRates) ? data.selectedSourceShippingRates[sourceCode] : null;
            }
            
        });
        
    };
    
});