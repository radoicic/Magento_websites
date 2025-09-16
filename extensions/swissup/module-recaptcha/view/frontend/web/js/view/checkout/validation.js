define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Swissup_Recaptcha/js/model/recaptcha-validator'
], function (Component, additionalValidators, recaptchaValidator) {
    'use strict';

    additionalValidators.registerValidator(recaptchaValidator);

    return Component.extend({});
});
