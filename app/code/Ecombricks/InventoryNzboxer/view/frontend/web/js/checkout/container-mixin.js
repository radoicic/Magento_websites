/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'underscore',
    'Magento_Checkout/js/model/quote'
], function (_, quote) {
    'use strict';
    
    return function (target) {
        return target.extend({
            addShippingMethodObserver: function () {
                quote.shippingMethod.subscribe(function (jointShippingMethod) {
                    if (!jointShippingMethod) {
                        return this.visible(false);
                    }
                    var shippingMethods = this.shippingMethods || '';
                    var isVisible = false;
                    _.each(quote.shippingMethods(), function (shippingMethod) {
                        if (shippingMethods.indexOf(shippingMethod.carrier_code + '_' + shippingMethod.carrier_code) !== -1) {
                            isVisible = true;
                        }
                    }, this);
                    this.visible(isVisible);
                }, this);
            }
        });
    };
});