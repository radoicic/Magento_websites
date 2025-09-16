define([
    'jquery',
    'ko'
], function ($, ko) {
    'use strict';

    function getRecapcthaSize($element) {
        const recaptcha = $element.data('swissupRecaptcha');

        return recaptcha ?
            recaptcha.options.size :
            '';
    }

    return function (expressCheckoutSmartButtons) {
        $(document).on('recaptchaexecuted', function (event) {
            var $recaptcha = $(event.target),
                recaptchaSize = getRecapcthaSize($recaptcha),
                $methodContent = $recaptcha.closest('.payment-method-content'),
                $paypalActions = $methodContent.find('#paypal-express-in-context-button'),
                uiPaypalInContext;

            if ($paypalActions.length
                && (recaptchaSize == 'normal' || recaptchaSize == 'compact')
            ) {
                uiPaypalInContext = ko.dataFor($paypalActions.get(0));
                if (uiPaypalInContext && uiPaypalInContext.renderPayPalButtons) {
                    $paypalActions.find('[id^="zoid-paypal"]').remove();
                    uiPaypalInContext.renderPayPalButtons($paypalActions.get(0));
                }
            }
        });

        return expressCheckoutSmartButtons;
    }
});
