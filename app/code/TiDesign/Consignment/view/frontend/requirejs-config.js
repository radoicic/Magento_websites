var config = {
	config: {
        mixins: {
            'Magento_InventoryConfigurableProductFrontendUi/js/configurable-variation-qty': {
                'TiDesign_Consignment/js/configurable-variation-qty': true
            }
        }
    },
    map: {
        '*': {
			'Magento_InventoryConfigurableProductFrontendUi/js/configurable-variation-qty':'TiDesign_Consignment/js/configurable-variation-qty'
        }
    }
};