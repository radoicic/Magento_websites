define([
    'jquery'
], function ($) {
    'use strict';

    /**
     * Get recaptcha jquery widget.
     *
     * @return {Object}
     */
    function getRecaptcha() {
        return $('.payment-method._active .g-recaptcha').data('swissupRecaptcha');
    }

    /**
     * Add recaptcha response to request.
     *
     * @param  {Object} paymentData
     */
    function assigner(paymentData) {
        var recaptcha = getRecaptcha();

        if (!recaptcha) {
            return;
        }

        if (paymentData['extension_attributes'] === undefined) {
            paymentData['extension_attributes'] = {};
        }

        paymentData['extension_attributes']['swissup_recaptcha_response'] = recaptcha.getResponse();
    }

    assigner.getRecaptcha = getRecaptcha;

    return assigner;
});
