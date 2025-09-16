/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'jquery'
], function ($) {
    'use strict';
    
    $.fn.serializeJson = function() {
        
        var json = {};
        var keyCounters = {};
        
        this.isInteger = function (value) {
            return value === parseInt(value, 10);
        };
        
        this.parseKey = function (key) {
            return key.match(/[a-zA-Z0-9_]+|(?=\[\])/g);
        };
        
        this.isArrayKey = function (key) {
            return key.match(/^$/);
        };
        
        this.isObjectKey = function (key) {
            return key.match(/^[a-zA-Z0-9_]+$/);
        };
        
        this.getCurrentKeyIndex = function (key) {
            if (keyCounters[key] === undefined) {
                keyCounters[key] = 0;
            }
            return keyCounters[key]++;
        };
        
        this.createKeyValue = function (key, value) {
            var target = this.isInteger(key) ? [] : {};
            target[key] = value;
            return target;
        };
        
        $.each($(this).serializeArray(), (function (parameterIndex, parameter) {
            var currentKey = parameter.name;
            var keys = this.parseKey(currentKey);
            var value = parameter.value;
            for (var keyIndex = keys.length - 1; keyIndex >= 0; keyIndex--) {
                var key = keys[keyIndex];
                currentKey = currentKey.replace(new RegExp("\\[" + key + "\\]$"), '');
                if (this.isArrayKey(key)) {
                    value = this.createKeyValue(this.getCurrentKeyIndex(currentKey), value);
                } else if (this.isObjectKey(key)) {
                    value = this.createKeyValue(key, value);
                }
            }
            json = $.extend(true, json, value);
        }).bind(this));
        return json;
    }
    
});