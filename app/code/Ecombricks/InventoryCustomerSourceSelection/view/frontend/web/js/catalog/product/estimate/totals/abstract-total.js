/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'underscore',
    'uiComponent',
    'Magento_Catalog/js/price-utils',
    'Ecombricks_InventoryCustomerSourceSelection/js/catalog/product/estimate/data'
], function (_, Component, priceUtils, data) {
    'use strict';
    
    return Component.extend({
        
        /**
         * Get source quote totals
         * 
         * @param {String} sourceCode
         * @returns {Object}
         */
        getSourceQuoteTotals: function (sourceCode) {
            return data.getSourceQuoteTotals(sourceCode);
        },
        
        /**
         * Get source quote totals segment
         * 
         * @param {String} sourceCode
         * @param {String} segmentCode
         * @returns {Object}
         */
        getSourceQuoteTotalsSegment: function (sourceCode, segmentCode) {
            var totals = this.getSourceQuoteTotals(sourceCode);
            if (!totals || !('total_segments' in totals)) {
                return null;
            }
            return _.find(totals.total_segments, function(segment) {
                return segment.code === segmentCode;
            });
        },
        
        /**
         * Get formatted price
         * 
         * @param {Number} price
         * @returns {String}
         */
        getFormattedPrice: function (price) {
            return priceUtils.formatPrice(price, data.priceFormat());
        }
        
    });
});