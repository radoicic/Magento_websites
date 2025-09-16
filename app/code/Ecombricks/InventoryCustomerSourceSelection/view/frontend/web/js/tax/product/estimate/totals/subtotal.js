/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'Ecombricks_InventoryCustomerSourceSelection/js/catalog/product/estimate/totals/abstract-total'
], function (Component) {
    'use strict';
    
    var displaySubtotalMode = window.checkoutConfig.reviewTotalsDisplayMode;
    
    return Component.extend({
        
        defaults: {
            displaySubtotalMode: displaySubtotalMode,
            template: 'Ecombricks_InventoryCustomerSourceSelection/tax/product/estimate/totals/subtotal'
        },
        
        /**
         * Check if is both prices displayed
         * 
         * @returns {Boolean}
         */
        isBothPricesDisplayed: function () {
            return this.displaySubtotalMode === 'both';
        },
        
        /**
         * Check if is including tax displayed
         * 
         * @returns {Boolean}
         */
        isIncludingTaxDisplayed: function () {
            return this.displaySubtotalMode === 'including';
        },
        
        /**
         * Get value including tax
         * 
         * @param {String} sourceCode
         * @returns {String}
         */
        getValueInclTax: function (sourceCode) {
            var price = 0;
            var totals = this.getSourceQuoteTotals(sourceCode);
            if (totals) {
                price = totals.subtotal_incl_tax;
            }
            return this.getFormattedPrice(price);
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
            if (totals) {
                price = totals.subtotal;
            }
            return this.getFormattedPrice(price);
        }
        
    });
});