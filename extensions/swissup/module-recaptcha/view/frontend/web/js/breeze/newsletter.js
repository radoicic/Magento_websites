(function () {
    'use strict';

    $(document).on('breeze:mount:Swissup_Recaptcha/js/newsletter', function (event, data) {
        $.swissup.recaptchaNewsletter(data.settings);
    });
})();
