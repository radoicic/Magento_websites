define([
    'jquery',
    'uiComponent',
    'Magento_Customer/js/action/login'
], function ($, Component, loginAction) {
    'use strict';

    var config;

    return Component.extend({
        recaptchaElement: null,
        defaults: {
            instantiateImmediately: false,
            template: 'Swissup_Recaptcha/checkout/login-recaptcha'
        },
        dataScope: 'global',

        /** @inheritdoc */
        initialize: function () {
            var me = this;

            me._super();

            if (window[me.configSource] && window[me.configSource].swissupRecaptcha) {
                config = window[me.configSource].swissupRecaptcha;
            }
            me.observe({
                isVisible: !!(config && config[me.formId])
            });
            loginAction.registerLoginCallback(me.loginCallback.bind(me));
        },

        /**
         * Callback after login to reset recaptcha
         * Multiple submitions with same value cause 'Invalide captcha' error
         */
        loginCallback: function () {
            var recaptcha = $(this.recaptchaElement)
                .data('swissupRecaptcha');

            if (recaptcha) {
                recaptcha.reset();
            }
        },

        /**
         * Get `data-mage-init` value.
         *
         * @return {String}
         */
        getMageInit: function () {
            return JSON.stringify({
                'Swissup_Recaptcha/js/recaptcha': config[this.formId]
            });
        },

        /**
         * Add listener to init recaptcha in `Sign In` dropdiwn on checkout
         */
        initListeners: function (element) {
            this.recaptchaElement = $(element).is('.g-recaptcha') ?
                $(element) :
                $(element, '.g-recaptcha');

            if (this.instantiateImmediately) {
                $(element).trigger('contentUpdated');

                return;
            }

            $('.block-authentication').on('modalopened', function () {
                $(element).trigger('contentUpdated');
            });
        }
    });
});
