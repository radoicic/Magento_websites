define([
    'jquery',
    'underscore',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/action/select-shipping-method',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Checkout/js/action/select-payment-method'
], function (
    $,
    _,
    wrapper,
    quote,
    checkoutData,
    selectShippingMethodAction,
    paymentService,
    selectPaymentMethodAction
) {
    'use strict';

    var checkoutConfig = window.checkoutConfig;

    return function (target) {
        if (!checkoutConfig || !checkoutConfig.isFirecheckout) {
            return target;
        }
        target.resolveShippingRates = wrapper.wrap(
            target.resolveShippingRates,
            function (originalAction, shippingRates) {
                var defaultShippingMethod = checkoutConfig.swissup.firecheckout.shipping.default_method;
                _.each(quote.getSources(), function (source) {
                    var sourceCode = source.source_code;
                    var sourceShippingRates = _.filter(shippingRates, function (shippingRate) {
                        return shippingRate.extension_attributes.source_code === sourceCode;
                    });
                    var shippingMethod = quote.getSourceShippingMethod(sourceCode);
                    if (sourceShippingRates.length === 1 && !shippingMethod) {
                        selectShippingMethodAction(sourceShippingRates[0]);
                        return;
                    }
                    var availableShippingRate = null;
                    if (shippingMethod) {
                        availableShippingRate = _.find(sourceShippingRates, function (shippingRate) {
                            return (shippingRate.carrier_code === shippingMethod.carrier_code) && 
                                (shippingRate.method_code === shippingMethod.method_code);
                        });
                    }
                    var selectedShippingRate = checkoutData.getSelectedSourceShippingRate(sourceCode);
                    if (!availableShippingRate && selectedShippingRate) {
                        availableShippingRate = _.find(sourceShippingRates, function (shippingRate) {
                            return shippingRate.carrier_code + '_' + shippingRate.method_code === selectedShippingRate;
                        });
                    }
                    if (!availableShippingRate && window.checkoutConfig.selectedShippingMethod) {
                        availableShippingRate = _.find(window.checkoutConfig.selectedShippingMethod, function (shippingRate) {
                            return shippingRate.extension_attributes.source_code === sourceCode;
                        });
                    }
                    if (!availableShippingRate && defaultShippingMethod) {
                        availableShippingRate = _.find(sourceShippingRates, function (shippingRate) {
                            return shippingRate.carrier_code + '_' + shippingRate.method_code === defaultMethod;
                        });
                    }
                    if (availableShippingRate) {
                        selectShippingMethodAction(availableShippingRate);
                    }
                }, this);
            }
        );
        target.resolvePaymentMethod = wrapper.wrap(
            target.resolvePaymentMethod,
            function (originalAction) {
                var availablePaymentMethods = paymentService.getAvailablePaymentMethods(),
                    selectedPaymentMethod = checkoutData.getSelectedPaymentMethod(),
                    defaultMethod = checkoutConfig.swissup.firecheckout.payment.default_method;
                if (availablePaymentMethods.length === 1) {
                    $(document.body).addClass('fc-single-payment-method');
                } else {
                    $(document.body).removeClass('fc-single-payment-method');
                }
                originalAction();
                if (!selectedPaymentMethod && defaultMethod) {
                    availablePaymentMethods.some(function (payment) {
                        if (payment.method === defaultMethod) {
                            selectPaymentMethodAction(payment);

                            return true;
                        }
                    });
                }
            }
        );
        return target;
    };
});