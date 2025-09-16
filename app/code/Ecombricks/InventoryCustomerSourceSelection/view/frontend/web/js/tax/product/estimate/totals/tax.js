/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'Ecombricks_InventoryCustomerSourceSelection/js/catalog/product/estimate/totals/abstract-total'
], function (Component) {
    'use strict';
    
    var isTaxDisplayedInGrandTotal = window.checkoutConfig.includeTaxInGrandTotal,
        isFullTaxSummaryDisplayed = window.checkoutConfig.isFullTaxSummaryDisplayed,
        isZeroTaxDisplayed = window.checkoutConfig.isZeroTaxDisplayed;
    
    return Component.extend({
        
        defaults: {
            isTaxDisplayedInGrandTotal: isTaxDisplayedInGrandTotal,
            notCalculatedMessage: 'Not yet calculated',
            template: 'Ecombricks_InventoryCustomerSourceSelection/tax/product/estimate/totals/tax'
        },
        
        /**
         * Check if is calculated
         * 
         * @param {String} sourceCode
         * @returns {Boolean}
         */
        isCalculated: function (sourceCode) {
            return this.getSourceQuoteTotalsSegment(sourceCode, 'tax') !== null;
        },
        
        /**
         * Get pure value
         * 
         * @param {String} sourceCode
         * @returns {Number}
         */
        getPureValue: function (sourceCode) {
            var price = 0;
            var segment = this.getSourceQuoteTotalsSegment(sourceCode, 'tax');
            if (segment) {
                price = segment.value;
            }
            return price;
        },

        /**
         * Get value
         * 
         * @param {String} sourceCode
         * @returns {String}
         */
        getValue: function (sourceCode) {
            if (!this.isCalculated(sourceCode)) {
                return this.notCalculatedMessage;
            }
            return this.getFormattedPrice(this.getPureValue(sourceCode));
        },
        
        /**
         * Check if show value
         * 
         * @param {String} sourceCode
         * @returns {Boolean}
         */
        ifShowValue: function (sourceCode) {
            if (this.getPureValue(sourceCode) === 0) {
                return isZeroTaxDisplayed;
            }
            return true;
        },
        
        /**
         * Check if show details
         * 
         * @param {String} sourceCode
         * @returns {Boolean}
         */
        ifShowDetails: function (sourceCode) {
            return this.getPureValue(sourceCode) > 0 && isFullTaxSummaryDisplayed;
        },
        
        /**
         * Format price
         * 
         * @param {Number} amount
         * @returns {String}
         */
        formatPrice: function (amount) {
            return this.getFormattedPrice(amount);
        },
        
        /**
         * Get percent amount
         * 
         * @param {*} amount
         * @param {*} totalPercentage
         * @param {*} percentage
         * @return {*|String}
         */
        getPercentAmount: function (amount, totalPercentage, percentage) {
            return parseFloat(amount * percentage / totalPercentage);
        },
        
        /**
         * Get tax amount
         * 
         * @param {*} parent
         * @param {*} percentage
         * @return {*|String}
         */
        getTaxAmount: function (parent, percentage) {
            var totalPercentage = 0;
            _.each(parent.rates, function (rate) {
                totalPercentage += parseFloat(rate.percent);
            });
            return this.getFormattedPrice(this.getPercentAmount(parent.amount, totalPercentage, percentage));
        },
        
        /**
         * Get details
         * 
         * @param {String} sourceCode
         * @returns {Array}
         */
        getDetails: function (sourceCode) {
            var segment = this.getSourceQuoteTotalsSegment(sourceCode, 'tax');
            if (segment && ('extension_attributes' in segment)) {
                return segment.extension_attributes.tax_grandtotal_details;
            }
            return [];
        }
        
    });
});