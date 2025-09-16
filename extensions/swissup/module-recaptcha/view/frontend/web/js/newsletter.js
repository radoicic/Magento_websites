(function (factory) {
    'use strict';

    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else {
        $.swissup = $.swissup || {};
        $.swissup.recaptchaNewsletter = factory($);
    }
}(function ($) {
    'use strict';

    var defaults = {
        form: 'form',
        destination: '.field:last',
        method: 'after',
        hidden: true
    };

    return function (options) {
        var $form;

        function _init() {
            $(options.destination, $form)[options.method](
                $(options.template).html()
            );
            $form.trigger('contentUpdated');
        }

        options = $.extend({}, defaults, options);
        $form = $(options.form);

        if (options.hidden) {
            $form.one('click', _init);
        } else {
            _init();
        }
    };
}));
