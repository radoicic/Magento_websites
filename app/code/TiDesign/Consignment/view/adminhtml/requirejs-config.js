var config = {
    map: {
        '*': {
            'select2':  'TiDesign_Consignment/js/select2.full.min',
			'passwordRequirements': 'TiDesign_Consignment/js/jquery.passwordrequirements.min',
			'Magento_InventoryConfigurableProductFrontendUi/js/configurable-variation-qty':'TiDesign_Consignment/js/configurable-variation-qty'
        }
    },
    shim: {
        'TiDesign_Consignment/js/jquery.passwordrequirements.min': {
            deps: ['jquery']
        }
    }
};