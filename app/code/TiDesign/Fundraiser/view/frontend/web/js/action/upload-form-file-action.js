define([
    'jquery'
], function ($) {
    'use strict';

    return function (form, uploadUrl) {
        form.append('form_key', window.FORM_KEY);
        return $.ajax({
            url: uploadUrl,
            type: "POST",
            dataType: "JSON",
            contentType: false,
            enctype: 'multipart/form-data',
            processData: false,
            data: form,
            showLoader: true,
            cache: false
        })
    }
});
