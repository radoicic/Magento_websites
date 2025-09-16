/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'Ecombricks_InventoryCustomerSourceSelection/js/catalog/product/estimate/totals/abstract-total',
    'Ecombricks_InventoryCustomerSourceSelection/js/catalog/product/estimate/data',
    'Magento_Checkout/js/model/quote'
], function (Component, data, quote) {
    'use strict';
    return Component.extend({

        defaults: {
            template: 'Ecombricks_InventoryCustomerSourceSelection/catalog/product/estimate/totals/shipping'
        },
        quoteIsVirtual: quote.isVirtual(),
        
        /**
         * Check if is calculated
         * 
         * @param {String} sourceCode
         * @returns {Boolean}
         */
        isCalculated: function (sourceCode) {
            return (data.getSourceQuoteShippingMethod(sourceCode)) ? true : false;
        },
        
        /**
         * Get value
         * 
         * @param {String} sourceCode
         * @returns {String}
         */
        getValue: function (sourceCode) {
            var price = 0;
            var totals = this.getSourceQuoteTotals(sourceCode);
            if (this.isCalculated(sourceCode) && totals) {
                price = totals.shipping_amount;
            }
            return this.getFormattedPrice(price);
        }
        
    });
});