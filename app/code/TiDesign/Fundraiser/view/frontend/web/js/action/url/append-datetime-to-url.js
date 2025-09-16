define([], function () {
    'use strict';

    return function (url) {
        const urlObj = new URL(url);
        urlObj.searchParams.set('t', new Date().getTime());
        return urlObj.toString();
    }
});
