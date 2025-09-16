define([
    'Magento_Ui/js/grid/columns/actions',
    'jquery',
    'uiRegistry'
], function (Actions, $, registry) {
    'use strict';

    return Actions.extend({
        defaults: {
            productId: '',
            productSku: '',
            productTypeId: ''
        },
        initObservable: function () {
            this._super().observe(['productId', 'productSku', 'productTypeId']);
            return this;
        },

        shouldShowButton: function (column) {
            return column().valid;
        },

        showPopup: function (column) {
            column = typeof column === 'object' ? column : column();
            this.productId(column.entity_id);
            this.productSku(column.sku);
            this.productTypeId(column.type_id);
            var modal = registry.get('product_listing.product_listing.pgrid_edit_sources_modal');
            modal.needReload = false;
            modal.setTitle(modal.options.title + ' for ' + column.name);
            registry.async(
                'pgrid_inventory_source_listing.pgrid_inventory_source_listing_data_source'
            )(function (grid) {
                grid.params.entity_id = this.productId();
                grid.params.sku = this.productSku();
                grid.params.type_id = this.productTypeId();
                grid.reload();
            }.bind(this));
            modal.openModal();
        }
    });
});
