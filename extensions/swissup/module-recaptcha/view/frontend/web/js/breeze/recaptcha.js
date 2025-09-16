(function () {
    'use strict';

    $(document).on('breeze:mount:Swissup_Recaptcha/js/recaptcha', function (event, data) {
        $.swissup.recaptchaApiLoader($.swissup.recaptcha.bind($.swissup, data.settings, data.el));
    });
})();
