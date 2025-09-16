/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'Ecombricks_InventoryCustomerSourceSelection/js/catalog/product/estimate/totals/abstract-total'
], function (Component) {
    'use strict';
    return Component.extend({

        defaults: {
            template: 'Ecombricks_InventoryCustomerSourceSelection/weee/product/estimate/totals/weee'
        },
        
        /**
         * Check if is displayed
         * 
         * @param {String} sourceCode
         * @returns {Boolean}
         */
        isDisplayed: function (sourceCode) {
            return this.getSourceQuoteTotalsSegment(sourceCode, 'weee') ? true : false;
        },
        
        /**
         * Get value
         * 
         * @param {String} sourceCode
         * @returns {String}
         */
        getValue: function (sourceCode) {
            var price = 0;
            var segment = this.getSourceQuoteTotalsSegment(sourceCode, 'weee');
            if (segment) {
                price = segment.value;
            }
            return this.getFormattedPrice(price);
        }
        
    });
});