var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/place-order': {
                'Swissup_Recaptcha/js/model/place-order-mixin': true
            },
            'Magento_Checkout/js/action/set-payment-information': {
                'Swissup_Recaptcha/js/model/place-order-mixin': true
            },
            'Magento_Checkout/js/action/set-payment-information-extended': {
                'Swissup_Recaptcha/js/model/place-order-mixin': true
            },
            // Paypal
            'Magento_Paypal/js/action/set-payment-method': {
                'Swissup_Recaptcha/js/model/place-order-mixin': true
            },
            // Amazon Payment
            'Amazon_Payment/js/action/place-order': {
                'Swissup_Recaptcha/js/model/place-order-mixin': true
            },
            // Mageplaza OSC
            'Mageplaza_Osc/js/action/set-payment-method': {
                'Swissup_Recaptcha/js/model/place-order-mixin': true
            },
            'Mageplaza_Osc/js/action/place-order': {
                'Swissup_Recaptcha/js/model/place-order-mixin': true
            },
            // CustomerParadigm Order Comments
            'CustomerParadigm_OrderComments/js/action/place-order': {
                'Swissup_Recaptcha/js/model/place-order-mixin': true
            },
            // Mageants Front Order Comments
            'Mageants_FrontOrderComment/js/action/place-order': {
                'Swissup_Recaptcha/js/model/place-order-mixin': true
            },
            // VLC Solutions Order Comments
            'VLCSolutions_OrderComments/js/action/place-order': {
                'Swissup_Recaptcha/js/model/place-order-mixin': true
            },
            // Ravedigital CoreUpdate
            'Ravedigital_CoreUpdate/js/action/place-order': {
                'Swissup_Recaptcha/js/model/place-order-mixin': true
            },
            // payment method EsteSolutions_ManualCCPayment
            'EsteSolutions_ManualCCPayment/js/action/place-order': {
                'Swissup_Recaptcha/js/model/place-order-mixin': true
            },
            // australian payment method ZipMoney_ZipMoneyPayment
            'ZipMoney_ZipMoneyPayment/js/action/set-payment-method': {
                'Swissup_Recaptcha/js/model/place-order-mixin': true
            },
            // This fixes weirdest bug ever in Swissup Recaptcha history.
            // After passing recaptcha paypal button doesn't react on clicks:
            //  - no reaction on all clicks in Magento 2.3.x;
            //  - no reaction on first click in Magneto 2.4.x.
            'Magento_Paypal/js/in-context/express-checkout-smart-buttons': {
                'Swissup_Recaptcha/js/model/paypal/rerender-in-context-buttons': true
            }
        }
    }
};
