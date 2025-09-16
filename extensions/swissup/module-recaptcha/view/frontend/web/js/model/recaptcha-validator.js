define([
    'jquery',
    './recaptcha-assigner',
    'mage/validation'
], function ($, recaptchaAssigner) {
    'use strict';

    var errorClass = '_error';

    /**
     * [addErrorMessage description]
     */
    function addErrorMessage(element) {
        var messageHtml = '<div generated="true" class="mage-error">' + $.validator.messages.required + '</div>';

        $(element).addClass(errorClass);

        if ($('.mage-error', element).length < 1) {
            $(element).append(messageHtml);
        }
    }

    /**
     * [removeErrorMessage description]
     */
    function removeErrorMessage(element) {
        $(element).removeClass(errorClass);
        $('.mage-error', element).remove();
    }

    return {
        /**
         * Validate recaptcha
         *
         * @returns {Boolean}
         */
        validate: function (hideError) {
            var isValid = true,
                recaptcha = recaptchaAssigner.getRecaptcha();

            if (recaptcha && recaptcha.options.size !== 'invisible') {
                isValid = !!recaptcha.getResponse();

                if (isValid) {
                    removeErrorMessage(recaptcha.element);
                } else if (!hideError) {
                    addErrorMessage(recaptcha.element);
                }

                // Add listener to validate Recaptcha after response
                $(recaptcha.element).one('recaptchaexecuted', function () {
                    removeErrorMessage(recaptcha.element);
                });
            }

            return isValid;
        }
    };
});
