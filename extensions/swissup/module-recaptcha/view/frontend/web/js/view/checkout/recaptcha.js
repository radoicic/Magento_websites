define([
    'Magento_Ui/js/lib/view/utils/async',
    'ko',
    'uiComponent',
    'Swissup_Recaptcha/js/recaptcha',
    'Magento_Checkout/js/model/quote'
], function ($, ko, Component, Recaptcha, quote) {
    'use strict';

    var config,
        recaptchaElement,
        lastTarget = null;

    /**
     * Get HTMLElement that represents payment method on checkout.
     *
     * @param  {String}      method
     * @return {HTMLElement}
     */
    function getPaymentMethodElement(method) {
        var element;

        $('.payment-method').each(function () {
            var uiComponent = ko.dataFor(this);

            if (uiComponent &&
                uiComponent.index === method
            ) {
                element = this;

                return false;
            }
        });

        return element;
    }

    /**
     * Find destination element for recaptcha
     *
     * @param  {HTMLElement} parentElement
     * @return {jQuery|null}
     */
    function getDestinationElement(parentElement) {
        var $destination = $('.checkout-recaptcha-block, .afterpay-checkout-note', parentElement),
            $agreements,
            $actionsToolbar;

        if ($destination.length) {
            return $destination;
        }

        /* Try to find agreements block and add recaptcha block aftre agreements */
        $agreements = $('.checkout-agreements-block', parentElement);
        // fallback for some customized checkouts
        $agreements = $agreements.length ?
            $agreements :
            $('.payment-method-' + quote.paymentMethod().method + ' .checkout-agreements-block');

        if ($agreements.length) {
            $destination = $('<div class="checkout-recaptcha-block"></div>');
            $agreements.after($destination);

            return $destination;
        }

        /* Try to find action toolbar with place order button and add recaptcha before toolbar */
        $actionsToolbar = $('.actions-toolbar:not([style="display: none;"])', parentElement);
        if ($actionsToolbar.length) {
            $destination = $('<div class="checkout-recaptcha-block"></div>');
            $actionsToolbar.before($destination)

            return $destination;
        }

        return null;
    }

    return Component.extend({
        defaults: {
            template: 'Swissup_Recaptcha/checkout/recaptcha',
            formId: null
        },

        /**
         * {@inheritdoc}
         */
        initialize: function () {
            var me = this;

            me._super();

            if (window[me.configSource] && window[me.configSource].swissupRecaptcha) {
                config = window[me.configSource].swissupRecaptcha;
            }

            $.async(
                {
                    selector: '.payment-method._active',
                    ctx: $('.form.payments').get(0)
                },
                me.moveRecaptcha.bind(me)
            );

            quote.paymentMethod.subscribe(function (paymentMethod) {
                var paymentMethodElement = getPaymentMethodElement(paymentMethod && paymentMethod.method);

                me.moveRecaptcha(paymentMethodElement);
            }, me);
        },

        /**
         * @param  {HTMLElement} element
         */
        initRecaptchaElement: function (element) {
            recaptchaElement = element;
            $(recaptchaElement).removeAttr('data-bind');
        },

        /**
         * Move recaptcha into payment method element.
         *
         * @param  {HTMLElement} target
         */
        moveRecaptcha: function (target) {
            var recaptchaWidget = $(recaptchaElement).data('swissupRecaptcha'),
                $destination;

            if (!target || target === lastTarget) {
                return;
            }

            lastTarget = target;
            $destination = getDestinationElement(target);

            if (!$destination || !$destination.length) {
                return;
            }

            $destination.first().append(recaptchaElement);

            // initialize recaptcha widget after move if still not inited
            if (!recaptchaWidget) {
                Recaptcha(config[this.formId], recaptchaElement);
            } else {
                recaptchaWidget.reset();
            }
        }
    });
});
