/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'underscore',
    'ko'
], function (_, ko) {
    'use strict';
    
    return {
        
        sku: ko.observable(window.checkoutConfig.productData.sku),
        priceFormat: ko.observable(window.checkoutConfig.priceFormat),
        storeCode: ko.observable(window.checkoutConfig.storeCode),
        isVirtual: ko.observable((window.checkoutConfig.productData.is_virtual) ? true : false),
        request: ko.observable({}),
        shippingAddress: ko.observable({}),
        shippingMethods: ko.observable({}),
        isLoading: ko.observable(false),
        isVisible: ko.observable(true),
        isEnabled: ko.observable(true),
        sources: ko.observableArray(window.checkoutConfig.productData.sources),
        sourceQuotes: ko.observableArray([]),
        
        /**
         * Get sources
         * 
         * @returns {Object[]}
         */
        getSources: function () {
            return this.sources();
        },
        
        /**
         * Set sources
         * 
         * @param {Object[]} sources
         * @returns {Object}
         */
        setSources: function (sources) {
            this.getSources().length = 0;
            ko.utils.arrayPushAll(this.getSources(), sources);
            this.sources.valueHasMutated();
            return this;
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
         * Get source quotes
         * 
         * @returns {Object[]}
         */
        getSourceQuotes: function () {
            return this.sourceQuotes();
        },
        
        /**
         * Set source quotes
         * 
         * @param {Object[]} sourceQuotes
         * @returns {Object}
         */
        setSourceQuotes: function (sourceQuotes) {
            this.sourceQuotes().length = 0;
            ko.utils.arrayPushAll(this.sourceQuotes(), sourceQuotes);
            this.sourceQuotes.valueHasMutated();
            return this;
        },
        
        /**
         * Get source quote
         * 
         * @param {String} sourceCode
         * @returns {Object}
         */
        getSourceQuote: function (sourceCode) {
            return _.find(this.sourceQuotes(), function (sourceQuote) { 
                return sourceQuote.source_code === sourceCode;
            });
        },
        
        /**
         * Get source quote shipping method
         * 
         * @param {String} sourceCode
         * @returns {String}
         */
        getSourceQuoteShippingMethod: function (sourceCode) {
            var sourceQuote = this.getSourceQuote(sourceCode);
            if (!sourceQuote || !sourceQuote.shipping_method) {
                return null;
            }
            return sourceQuote.shipping_method;
        },
        
        /**
         * Set source quote shipping method
         * 
         * @param {String} sourceCode
         * @param {String} shippingMethod
         * @returns {Object}
         */
        setSourceQuoteShippingMethod: function (sourceCode, shippingMethod) {
            var sourceQuote = this.getSourceQuote(sourceCode);
            if (!sourceQuote) {
                return this;
            }
            sourceQuote.shipping_method = shippingMethod;
            var shippingMethods = this.shippingMethods();
            shippingMethods[sourceCode] = shippingMethod;
            this.shippingMethods(shippingMethods);
            this.sourceQuotes.valueHasMutated();
            return this;
        },
        
        /**
         * Get source quote shipping rates
         * 
         * @param {String} sourceCode
         * @returns {Object[]}
         */
        getSourceQuoteShippingRates: function (sourceCode) {
            var sourceQuote = this.getSourceQuote(sourceCode);
            if (!sourceQuote || !sourceQuote.shipping_rates) {
                return [];
            }
            return sourceQuote.shipping_rates;
        },
        
        /**
         * Get source quote carriers
         * 
         * @param {String} sourceCode
         * @returns {Object[]}
         */
        getSourceQuoteCarriers: function (sourceCode) {
            var carriers = [];
            _.each(this.getSourceQuoteShippingRates(sourceCode), function (shippingRate) {
                if (_.find(carriers, function (carrier) { return carrier.code === shippingRate.carrier_code; })) {
                    return;
                }
                carriers.push({
                    code: shippingRate.carrier_code,
                    title: shippingRate.carrier_title
                });
            });
            return carriers;
        },
        
        /**
         * Get source quote carrier shipping rates
         * 
         * @param {String} sourceCode
         * @param {String} carrierCode
         * @returns {Object[]}
         */
        getSourceQuoteCarrierShippingRates: function (sourceCode, carrierCode) {
            return _.filter(this.getSourceQuoteShippingRates(sourceCode), function (shippingRate) {
                return carrierCode === shippingRate.carrier_code;
            });
        },
        
        /**
         * Get source quote totals
         * 
         * @param {String} sourceCode
         * @returns {Object}
         */
        getSourceQuoteTotals: function (sourceCode) {
            var sourceQuote = this.getSourceQuote(sourceCode);
            if (!sourceQuote || !sourceQuote.totals) {
                return null;
            }
            return sourceQuote.totals;
        }
    };
});