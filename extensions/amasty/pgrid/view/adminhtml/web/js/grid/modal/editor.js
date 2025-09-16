define([
    'jquery',
    'uiRegistry',
    'Magento_Ui/js/grid/editing/editor'
], function ($, registry, Editor) {
    'use strict';

    return Editor.extend({
        save: function () {
            var modal = registry.get('product_listing.product_listing.pgrid_edit_sources_modal');
            modal.needReload = true;
            this._super();
        }
    });
});
