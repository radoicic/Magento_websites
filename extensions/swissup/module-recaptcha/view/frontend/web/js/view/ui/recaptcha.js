define([
    'jquery',
    'uiComponent',
    'Swissup_Recaptcha/js/recaptcha'
], function ($, Component, Recaptcha) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Swissup_Recaptcha/checkout/recaptcha',
            formId: null,
            configSource: null
        },

        /**
         * @param  {HTMLElement} element
         */
        initRecaptchaElement: function (element) {
            $(element).removeAttr('data-bind');
            Recaptcha(this.configSource, element);
        }
    });
});
