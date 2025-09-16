define([
	'jquery',
    'Magento_Ui/js/grid/columns/column'
], function ($,Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'TiDesign_ProductGridFix/product/grid/cell/source-items.html',
            itemsToDisplay: 5,
			fieldClass: {
                'data-grid-html-cell': true
            }
        },

        /**
         * Get source items data (source name and qty)
         *
         * @param {Object} record - Record object
         * @returns {Array} Result array
         */
        getSourceItemsData: function (record) {
            return record[this.index] ? record[this.index] : [];
        },

        /**
         * @param {Object} record - Record object
         * @returns {Array} Result array
         */
        getSourceItemsDataCut: function (record) {
            return this.getSourceItemsData(record).slice(0, this.itemsToDisplay);
        },


        getTidesignData: function (record) {
			return record['tidesign_product_data'];
        },
		
		getTidesignId: function (record) {
			return record['entity_id'];
        },
		
		getTidesignWebsite: function (record) {
			return record['tidesign_website_data'];
		},
		
		getTidesignProductName: function (record) {
			return record['tidesign_product_name'];
		},
		
		
		getTidesignProductImage: function (record) {
			return record['tidesign_product_image'];
		},
		
		
        getFieldHandler: function (row) {
            return null;
        }
    });
});
