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
            template: 'Ecombricks_InventoryCustomerSourceSelection/catalog/product/estimate/totals/subtotal'
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