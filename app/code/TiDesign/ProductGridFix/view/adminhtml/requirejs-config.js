var config = {
    config: {
        mixins: {
            'Magento_InventoryCatalogAdminUi/js/product/grid/cell/quantity-per-source': {
                'TiDesign_ProductGridFix/js/quantity-per-source': true
            }
        }
    },
    map: {
        '*': {
			'fancyboxuby': 'TiDesign_ProductGridFix/js/fancyboxuby.min'
        }
    },
    shim: {
        'TiDesign_ProductGridFix/js/fancyboxuby.min': {
            deps: ['jquery']
        }
    }
};