(function () {
    'use strict';

    function loadJs (src, callback) {
        var script = document.createElement('script');

        script.src = src;
        script.type = 'text/javascript';
        script.onload = callback;
        $('head').append(script);
    };

    $.swissup = $.swissup || {};
    $.swissup.recaptchaApiLoader = function (callback) {
        var apiUrl = $.swissup.recaptcha.apiUrl;

        if (typeof grecaptcha !== 'undefined') {
            callback();
        } else {
            $(document).one('grecaptcha:loaded', callback);
            if (!$('[src="' + apiUrl + '"]').length) {
                loadJs(apiUrl, $(document).trigger.bind($(document), 'grecaptcha:loaded'));
            }
        }
    }
})();
