/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
var config = {
    map: {
        '*': {
            priceBox: 'Ecombricks_InventoryCustomerSourceSelection/js/catalog/product/price-box'
        }
    },
    config: {
        mixins: {
            'Magento_Bundle/js/price-bundle': {
                'Ecombricks_InventoryCustomerSourceSelection/js/bundle/price-bundle-mixin': true
            },
            'Magento_ConfigurableProduct/js/configurable': {
                'Ecombricks_InventoryCustomerSourceSelection/js/configurable-product/configurable-mixin': true
            },
            'Magento_Swatches/js/swatch-renderer': {
                'Ecombricks_InventoryCustomerSourceSelection/js/swatches/swatch-renderer-mixin': true
            }/*,
            'Magento_Checkout/js/action/select-shipping-method': {
                'Ecombricks_InventoryCustomerSourceSelection/js/checkout/action/select-shipping-method-mixin': true
            },
            'Magento_Checkout/js/model/quote': {
                'Ecombricks_InventoryCustomerSourceSelection/js/checkout/model/quote-mixin': true
            },
            'Magento_Checkout/js/model/checkout-data-resolver': {
                'Ecombricks_InventoryCustomerSourceSelection/js/checkout/model/checkout-data-resolver-mixin': true
            },
            'Magento_Checkout/js/view/cart/shipping-rates': {
                'Ecombricks_InventoryCustomerSourceSelection/js/checkout/view/cart/shipping-rates-mixin': true
            },
            'Magento_Checkout/js/view/shipping': {
                'Ecombricks_InventoryCustomerSourceSelection/js/checkout/view/shipping-mixin': true
            },
            'Magento_Checkout/js/view/shipping-information': {
                'Ecombricks_InventoryCustomerSourceSelection/js/checkout/view/shipping-information-mixin': true
            },
            'Magento_Checkout/js/view/summary/shipping': {
                'Ecombricks_InventoryCustomerSourceSelection/js/checkout/view/summary/shipping-mixin': true
            },
            'Magento_Checkout/js/checkout-data': {
                'Ecombricks_InventoryCustomerSourceSelection/js/checkout/checkout-data-mixin': true
            },	            
            'Magento_Paypal/js/order-review': {
                'Ecombricks_InventoryCustomerSourceSelection/js/paypal/order-review-mixin': true
            }*/
        }
    }
};