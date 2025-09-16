define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    './recaptcha-assigner'
], function ($, wrapper, quote, recaptchaAssigner) {
    'use strict';

    var deferred = $.Deferred();

    deferred.success = deferred.done.bind(deferred);

    /**
     * @param  {String} needle
     * @param  {String} haystack
     * @return {Boolean}
     */
    function startsWith(needle, haystack) {
        return haystack.lastIndexOf(needle, 0) === 0
    }

    /**
     * @param  {Array}  args
     * @return {Object}
     */
    function resolvePaymentData(args) {
        var paymentData, currentPaymentMethod;

        currentPaymentMethod = quote.paymentMethod().method;

        if (args.length &&
            args[0] &&
            args[0].method === currentPaymentMethod
        ) {
            // First argument is paymentData. Valid for:
            //  - Magento_Checkout/js/action/place-order;
            //  - Magento_Paypal/js/action/set-payment-method;
            //  - Amazon_Payment/js/action/place-order;
            //  - Mageplaza_Osc/js/action/place-order;
            //  - Mageplaza_Osc/js/action/set-payment-method.
            paymentData = args[0];
        } else if (args.length &&
            args[0] &&
            startsWith(args[0].method, currentPaymentMethod)
        ) {
            // Vault for cards used. Valid for:
            //  - PayPal_Braintree (vault);
            //  - Swarming_SubscribePro (vault).
            paymentData = args[0];
        } else if (args.length > 1 &&
            args[1] &&
            args[1].method === currentPaymentMethod
        ) {
            // Second argument is paymentData. Valid for:
            //  - Magento_Checkout/js/action/set-payment-information;
            //  - Magento_Checkout/js/action/set-payment-information-extended.
            paymentData = args[1];
        } else {
            // paymentData isn't supplied. Get it from quote
            paymentData = quote.paymentMethod();
        }

        return paymentData;
    }

    return function (action) {

        /** Override default [place order | set payment info | set payment method]
         *  action and add recaptcha string to request.
         */
        return wrapper.wrap(action, function () {
            var args = Array.prototype.slice.call(arguments),
                originalAction = args.shift(args),
                recaptcha = recaptchaAssigner.getRecaptcha(),
                paymentData;

            paymentData = resolvePaymentData(args);

            if (recaptcha &&
                recaptcha.options.size === 'invisible' &&
                !recaptcha.getResponse()
            ) {
                // It is invisible recaptcha. We have to postpone original action.
                // And call it when recaptcha response received.
                recaptcha.element.one('recaptchaexecuted', function () {
                    recaptchaAssigner(paymentData);
                    originalAction.apply(null, args)
                        .done(deferred.resolve)
                        .fail(deferred.reject);
                });
                recaptcha.execute();

                return deferred;

            }

            recaptchaAssigner(paymentData);

            return originalAction.apply(null, args);
        });
    };
});
