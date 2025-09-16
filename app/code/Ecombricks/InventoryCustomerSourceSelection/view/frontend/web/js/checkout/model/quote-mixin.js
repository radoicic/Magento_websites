/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'ko',
    'underscore'
], function (ko, _) {
    'use strict';
    
    return function (target) {
        
        var sources = ko.observableArray(('sources' in window.checkoutConfig.quoteData) ? window.checkoutConfig.quoteData.sources : []);
        var shippingMethods = ko.observableArray([]);
        
        /**
         * Get source shipping method
         * 
         * @param {String} sourceCode
         * @returns {Object}
         */
        var getSourceShippingMethod = function (sourceCode) {
            return _.find(shippingMethods(), function (shippingMethod) {
                return sourceCode === shippingMethod.extension_attributes.source_code;
            });
        };
        
        /**
         * Set source shipping method
         * 
         * @param {String} sourceCode
         * @param {Object} shippingMethod
         * @returns {void}
         */
        var setSourceShippingMethod = function (sourceCode, shippingMethod) {
            var sourceShippingMethod = getSourceShippingMethod(sourceCode);
            if (sourceShippingMethod) {
                shippingMethods.replace(sourceShippingMethod, shippingMethod);
            } else {
                shippingMethods.push(shippingMethod);
            }
        };
        
        /**
         * Check if has joint shipping method
         * 
         * @returns {Boolean}
         */
        var hasJointShippingMethod = function () {
            if (sources().length === 0) {
                return false;
            }
            if (shippingMethods().length === 0) {
                return false;
            }
            return _.every(sources(), function (source) {
                var shippingMethod = getSourceShippingMethod(source.source_code);
                return (shippingMethod && ('method_code' in shippingMethod)) ? true : false;
            }, this);
        };
        
        /**
         * Get joint shipping method
         * 
         * @returns {Object}
         */
        var getJointShippingMethod = function () {
            if (!hasJointShippingMethod()) {
                return null;
            }
            return {
                carrier_code: (_.map(shippingMethods(), function (shippingMethod) {
                    return shippingMethod.extension_attributes.source_code + ':' + shippingMethod.carrier_code;
                })).join('|'),
                method_code: (_.map(shippingMethods(), function (shippingMethod) {
                    return shippingMethod.extension_attributes.source_code + ':' + shippingMethod.method_code;
                })).join('|')
            };
        };
        
        var shippingMethod = ko.computed(function() { 
            return getJointShippingMethod();
        });
        
        return _.extend(target, {
            
            sources: sources,
            shippingMethods: shippingMethods,
            shippingMethod: shippingMethod,
            
            /**
             * Get sources
             * 
             * @returns {Object[]}
             */
            getSources: function () {
                return this.sources();
            },
            
            /**
             * Get source
             * 
             * @param {String} sourceCode
             * @returns {Object}
             */
            getSource: function (sourceCode) {
                return _.find(this.getSources(), function(source) {
                    return source.source_code === sourceCode;
                }, this);
            },
            
            /**
             * Set source name
             * 
             * @param {String} sourceCode
             * @returns {String}
             */
            getSourceName: function (sourceCode) {
                var source = this.getSource(sourceCode);
                return source ? source.name : '';
            },
            
            /**
             * Set source shipping method
             * 
             * @param {String} sourceCode
             * @param {Object} shippingMethod
             * @returns {Object}
             */
            setSourceShippingMethod: function (sourceCode, shippingMethod) {
                setSourceShippingMethod(sourceCode, shippingMethod);
                return this;
            },
            
            /**
             * Get source shipping method
             * 
             * @param {String} sourceCode
             * @returns {Object}
             */
            getSourceShippingMethod: function (sourceCode) {
                return getSourceShippingMethod(sourceCode);
            },
            
            /**
             * Check if has joint shipping method
             * 
             * @returns {Boolean}
             */
            hasJointShippingMethod: function () {
                return hasJointShippingMethod();
            }
        });
    };
});