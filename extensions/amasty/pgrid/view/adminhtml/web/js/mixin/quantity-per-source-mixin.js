define([
    'jquery',
    'Amasty_Pgrid/js/model/column',
], function ($, amColumn) {
    'use strict';

    return function (target) {
        return target.extend(amColumn)
    };
});
