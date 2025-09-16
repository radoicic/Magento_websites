/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'jquery',
    'Magento_Sales/order/create/scripts'
], function(jQuery) {
    
    AdminOrderPrototype = AdminOrder.prototype;
    
    AdminOrder.prototype = jQuery.extend({}, AdminOrder.prototype, {
        
        sourceCodes: [],
        shippingMethods: {},
        
        /**
         * Set source codes
         * 
         * @param {Array} sourceCodes
         * @returns {Object}
         */
        setSourceCodes : function(sourceCodes) {
            this.sourceCodes = sourceCodes;
            return this;
        },
        
        /**
         * Set shipping methods
         * 
         * @param {Object} shippingMethods
         * @returns {Object}
         */
        setShippingMethods : function(shippingMethods) {
            this.shippingMethods = jQuery.isPlainObject(shippingMethods) ? shippingMethods : {};
            return this;
        },
        
        /**
         * Check if shipping method is selected
         * 
         * @returns {Boolean}
         */
        isShippingMethodSelected : function () {
            if (this.shippingMethods.length === 0) {
                return false;
            }
            var isSelected = true;
            jQuery.each(this.sourceCodes, (function(index, sourceCode) {
                if (!(sourceCode in this.shippingMethods)) {
                    isSelected = false;
                    return false;
                }
            }).bind(this));
            return isSelected;
        },
        
        /**
         * Set source shipping method
         * 
         * @param {String} sourceCode
         * @param {String} method
         * @returns {Object}
         */
        setSourceShippingMethod : function(sourceCode, method){
            var data = {};
            this.shippingMethods[sourceCode] = method;
            if (this.isShippingMethodSelected()) {
                jQuery.each(this.sourceCodes, (function(index, sourceCode) {
                    data['order[shipping_method][' + sourceCode + ']'] = this.shippingMethods[sourceCode];
                }).bind(this));
                this.loadArea(['shipping_method', 'totals', 'billing_method'], true, data);
            }
            return this;
        }
        
    });
    
});