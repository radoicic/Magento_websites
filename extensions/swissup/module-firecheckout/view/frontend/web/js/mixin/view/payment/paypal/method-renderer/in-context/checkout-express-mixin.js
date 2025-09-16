define([
    'jquery'
], function ($) {
    'use strict';

    var checkoutConfig = window.checkoutConfig;

    return function (target) {
        if (!checkoutConfig || !checkoutConfig.isFirecheckout) {
            return target;
        }

        return target.extend({
            /**
             * @param {HTMLElement} context
             */
            initListeners: function (context) {
                $('#customer-email').on('change', function () {
                    this.validate();
                }.bind(this));

                return this._super(context);
            }
        });
    };
});
