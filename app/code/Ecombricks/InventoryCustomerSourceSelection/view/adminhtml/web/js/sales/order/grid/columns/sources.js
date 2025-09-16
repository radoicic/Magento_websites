/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'Magento_Ui/js/grid/columns/column'
], function (Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Ecombricks_InventoryCustomerSourceSelection/sales/order/grid/columns/sources.html',
            itemsToDisplay: 5
        },

        /**
         * @param {Array} record
         * @returns {Array}
         */
        getTooltipData: function (record) {
            return record[this.index];
        },

        /**
         * @param {Object} record - Record object
         * @returns {Array} Result array
         */
        getSources: function (record) {
            return this.getTooltipData(record).slice(0, this.itemsToDisplay);
        }
    });
});