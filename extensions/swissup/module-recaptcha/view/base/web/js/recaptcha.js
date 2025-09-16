/* global grecaptcha */
(function (factory) {
    'use strict';

    var recaptchaApiUrl = '//www.google.com/recaptcha/api.js' +
            '?hl=' + document.documentElement.lang.substring(0, 2) +
            '&render=explicit';

    if (typeof define === 'function' && define.amd) {
        define([
            'jquery',
            'Magento_Ui/js/modal/modal', // 2.3.3: create 'jquery-ui-modules/widget' dependency
            recaptchaApiUrl
        ], factory);
    } else {
        factory($).apiUrl = recaptchaApiUrl;
    }
}(function ($) {
    'use strict';

    $.widget('swissup.recaptcha', {

        options: {
            sitekey: '',
            theme: 'light',
            size: 'normal',
            instantVerification: false,
            hookFormSubmit: false,
            imageLoader: '',
            minHeight: '78px' // height of recaptcha placeholder
        },

        /**
         * @private
         */
        _create: function () {
            var me = this;

            if (me.isVisibleInline()) {
                me.showLoader();
            }

            // add callback after Google response
            $.extend(me.options, {
                callback: me.onExecuteCallback.bind(me),
                'expired-callback': me.reset.bind(me)
            });
            me._bind();
            grecaptcha.ready(me.render.bind(me));
        },

        /**
         * @private
         */
        _bind: function () {
            var handlers,
                me = this;

            if (me.options.hookFormSubmit) {
                me.getForm().on('submit', me.onFormSubmit.bind(me));
                if (typeof jQuery !== 'undefined') {
                    // force my submit listener to run first
                    // inspired by https://stackoverflow.com/a/13980262
                    handlers = jQuery._data(me.getForm().get(0)).events.submit;
                    handlers.unshift(handlers.pop());
                }
            }
        },

        /**
         * render reCAPTCHA and set widgetId
         */
        render: function () {
            var me = this,
                widgetId = me.getWidgetId();

            if (typeof widgetId === 'undefined') {
                // g-recaptcha element can't have `data-bind` attributes.
                widgetId = grecaptcha.render(
                    me.element.removeAttr('data-bind').get(0),
                    me.options
                );
                $(me.element).attr('data-widget-id', widgetId);
            }

            if (me.options.instantVerification) {
                me.execute();
            }
        },

        /**
         * Execute reCAPTCHA
         */
        execute: function () {
            var widgetId = this.getWidgetId();

            if (typeof widgetId === 'undefined') {
                return false;
            }

            grecaptcha.execute(widgetId);

            return true;
        },

        /**
         * Get reCAPTCHA widget ID
         *
         * @return {Number} widget ID
         */
        getWidgetId: function () {
            return $(this.element).attr('data-widget-id');
        },

        /**
         * Refresh reCAPTCHA
         */
        reset: function () {
            grecaptcha.reset(this.getWidgetId());
        },

        /**
         * Show loader
         */
        showLoader: function () {
            var me = this;

            if (me.options.imageLoader) {
                me.element.css('min-height', me.options.minHeight);
                me.element.css(
                    'background',
                    'url(' + me.options.imageLoader + ') no-repeat ' +
                        ($('body').hasClass('rtl') ? 'right' : 'left') +
                        ' center'
                );
            }
        },

        /**
         * Check if recaptcha visible or inline (will it take any space in form)
         *
         * @return {Boolean}
         */
        isVisibleInline: function () {
            var options = this.options;

            return options.size === 'invisible' && options.badge === 'inline' ||
                options.size !== 'invisible';
        },

        /**
         * Get form
         *
         * @return {jQuery}
         */
        getForm: function () {
            return this.element.closest('form');
        },

        /**
         * Get current token value
         *
         * @return {String}
         */
        getResponse: function () {
            if (this.isReadyToUse()) {
                try {
                    return grecaptcha.getResponse(this.getWidgetId());
                } catch (error) {
                    return '';
                }
            }

            return '';
        },

        /**
         * Callback when 'execute' method complited (only for invisible reCAPTCHA)
         * callback excepts parameter 'token' - recaptcha response
         */
        onExecuteCallback: function () {
            var me = this;

            me._trigger('executed');

            if (me._formSubmitHooked) {
                me._formSubmitHooked = false;
                me.hideFormLoader();
                me.getForm().submit();
            }
        },

        /**
         * Event processor of form submit
         *
         * @param  {jQuery.Event} event
         */
        onFormSubmit: function (event) {
            var notReadyMessage = 'ReCAPTCHA still loading. Please try one more time in a second.',
                me = this;

            if (!me.getResponse()) {
                if (me.isReadyToUse()) {
                    me.showFormLoader();
                    me._formSubmitHooked = true;
                    me.execute();
                } else {
                    alert(notReadyMessage); // eslint-disable-line no-alert
                }

                event.preventDefault();
                event.stopImmediatePropagation();

                return false;
            }
        },

        /**
         * Show form loader while waiting for recaptcha response
         */
        showFormLoader: function () {
            var $form = this.getForm();

            $('*', $form)
                .css('opacity', '.8')
                .css('pointer-events', 'none');
            $form.css(
                'background',
                'url(' + this.options.imageLoader + ') no-repeat center'
            );
        },

        /**
         * Hide form loader
         */
        hideFormLoader: function () {
            var $form = this.getForm();

            $('*', $form)
                .css('opacity', '')
                .css('pointer-events', '');
            $form.css('background', '');
        },

        /**
         * @return {Boolean}
         */
        isReadyToUse: function () {
            return typeof grecaptcha !== 'undefined' &&
                typeof grecaptcha.getResponse === 'function';
        }
    });

    return $.swissup.recaptcha;
}));
