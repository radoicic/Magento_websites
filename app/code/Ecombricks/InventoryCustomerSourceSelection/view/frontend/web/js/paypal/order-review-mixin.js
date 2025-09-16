/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'jquery',
    'jquery-ui-modules/widget'
], function ($) {
    'use strict';
    
    (function() {
        var originalVal = $.fn.remove;
        $.fn.val = function() {
            var firstElement = this[0];
            if (
                !firstElement || 
                firstElement.nodeType !== 1 || 
                firstElement.type !== 'select-one' || 
                !$(firstElement).data('source')
            ) {
                return originalVal.apply(this, arguments);
            }
            var shippingMethods = {};
            this.each(function () {
                if (!this.value) {
                    shippingMethods = {};
                    return false;
                }
                shippingMethods[$(this).data('sourceCode')] = this.value;
            });
            if ($.isEmptyObject(shippingMethods)) {
                return '';
            }
            return JSON.stringify(shippingMethods);
        }
    })();
    
    return function (target) {
        $.widget('mage.orderReview', target, {
            
            /**
             * Ajax shipping update
             */
            _ajaxShippingUpdate: function (shippingMethod) {
                return this._super(JSON.parse(shippingMethod));
            }
            
        });
        return $.mage.orderReview;
    };
});