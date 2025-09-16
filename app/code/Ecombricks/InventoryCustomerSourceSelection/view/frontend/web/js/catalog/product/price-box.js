/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'jquery',
    'underscore',
    'Ecombricks_InventoryCustomerSourceSelection/js/catalog/product/source',
    'Magento_Catalog/js/price-box',
    'jquery-ui-modules/widget'
], function ($, _, source) {
    'use strict';
    
    $.widget('mage.priceBox', $.mage.priceBox, {
        
        /**
         * Create
         * 
         * @returns {Object}
         */
        _create: function () {
            this._super();
            source.onChange(this.onSourceChange.bind(this));
            this.changeSource();
            return this;
        },
        
        /**
         * On source change
         * 
         * @returns {Object}
         */
        onSourceChange: function () {
            this.changeSource();
            return this;
        },
        
        /**
         * Change source
         * 
         * @returns {Object}
         */
        changeSource: function () {
            var sourceCode = source.getValue();
            if (!sourceCode || this.options.priceConfig.sourcePrices || !this.options.priceConfig.sourcePrices[sourceCode]) {
                return this;
            }
            var originalPrices = this.options.prices;
            var sourcePrices = this.options.priceConfig.sourcePrices[sourceCode];
            var pricesDiff = {};
            _.each(originalPrices, function (price, priceCode) {
                if (sourcePrices[priceCode]) {
                    pricesDiff[priceCode] = sourcePrices[priceCode].amount - price.amount;
                }
            }, this);
            _.each(this.cache.displayPrices, function (price, priceCode) {
                if (pricesDiff[priceCode]) {
                    price.amount = price.amount + pricesDiff[priceCode];
                }
            }, this);
            this.reloadPrice();
            this.options.prices = sourcePrices;
            return this;
        }
        
    });
    return $.mage.priceBox;
    
});