define([
    'Magento_Ui/js/modal/modal-component',
    'uiRegistry'
], function (ModalComponent, registry) {
    'use strict';

    return ModalComponent.extend({
        closeModal: function () {
            var modal = registry.get('product_listing.product_listing.pgrid_edit_sources_modal');
            if (modal.needReload) {
                var productGrid = registry.get("product_listing.product_listing_data_source");
                productGrid.params.random = Math.random();
                productGrid.reload();
            }
            registry.get('pgrid_inventory_source_listing.pgrid_inventory_source_listing.pgrid_inventory_source_listing_columns.ids').deselectAll();
            this._super();
        }
    });
});
