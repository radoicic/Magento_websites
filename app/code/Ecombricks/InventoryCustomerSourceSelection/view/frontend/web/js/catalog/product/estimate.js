/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'underscore',
    'ko',
    'uiComponent',
    'mage/storage',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/model/shipping-rates-validator',
    'Ecombricks_InventoryCustomerSourceSelection/js/catalog/product/estimate/data',
    'Ecombricks_InventoryCustomerSourceSelection/js/catalog/product/product',
    'Ecombricks_InventoryCustomerSourceSelection/js/catalog/product/source'
], function (_, ko, Component, storage, errorProcessor, shippingRatesValidator, data, product, source) {
    'use strict';
    return Component.extend({
        
        defaults: {
            template: 'Ecombricks_InventoryCustomerSourceSelection/catalog/product/estimate'
        },
        
        sourceQuotesCache: {},
        isVirtual: data.isVirtual,
        isLoading: data.isLoading,
        isVisible: data.isVisible,
        isEnabled: data.isEnabled,
        isAddressDefined: ko.observable(false),
        
        /**
         * Initialize
         * 
         * @returns {Object}
         */
        initialize: function () {
            this._super();
            this.initProductForm();
            this.initSource();
            return this;
        },
        
        /**
         * Initialize product form
         * 
         * @returns {Object}
         */
        initProductForm: function () {
            data.request(product.getJson());
            product.getForm().on('change', (function() {
                data.request(product.getJson());
            }).bind(this));
            data.request.subscribe(function () {
                if (this.isAddressDefined()) {
                    this.reloadSourceQuotes();
                }
            }, this);
            return this;
        },
        
        /**
         * Initialize source
         * 
         * @returns {Object}
         */
        initSource: function () {
            if (!this.source) {
                return this;
            }
            data.shippingAddress(this.source.shippingAddress);
            this.source.on('shippingAddress', (function (address) {
                data.shippingAddress(address);
                this.isAddressDefined(true);
            }).bind(this));
            data.shippingAddress.subscribe(function () {
                this.reloadSourceQuotes();
            }, this);
            return this;
        },
        
        /**
         * Initialize element
         * 
         * @returns {Object}
         */
        initElement: function (element) {
            this._super();
            if (element.index !== 'address') {
                return this;
            }
            shippingRatesValidator.bindChangeHandlers(element.elems(), true, 1000);
            element.elems.subscribe(function (elems) {
                shippingRatesValidator.doElementBinding(elems[elems.length - 1], true, 1000);
            });
            return this;
        },
        
        /**
         * Reload source quotes
         * 
         * @returns {Object}
         */
        reloadSourceQuotes: function () {
            if (!product.isValid()) {
                return this;
            }
            this.isLoading(true);
            var query = JSON.stringify({
                address: data.shippingAddress(),
                request: _.extend(data.request(), { shipping_methods: data.shippingMethods() })  
            });
            if (query in this.sourceQuotesCache) {
                data.setSourceQuotes(this.sourceQuotesCache[query]);
                this.isLoading(false);
                return this;
            }
            storage
                .post('rest/' + data.storeCode() + '/V1/products/' + data.sku() + '/source-quotes', query, false)
                .done((function (result) {
                    data.setSourceQuotes(result);
                    this.sourceQuotesCache[query] = result;
                }).bind(this))
                .fail(function (response) {
                    data.setSourceQuotes([]);
                    errorProcessor.process(response);
                })
                .always((function () {
                    this.isLoading(false);
                }).bind(this));
            return this;
        },
        
        /**
         * Get source
         * 
         * @param {String} sourceCode
         * @returns {Object}
         */
        getSource: function (sourceCode) {
            return data.getSource(sourceCode);
        },
        
        /**
         * Get source quotes
         * 
         * @returns {Object[]}
         */
        getSourceQuotes: function () {
            return data.getSourceQuotes();
        },
        
        /**
         * Get source quote carriers
         * 
         * @param {String} sourceCode
         * @returns {Object[]}
         */
        getSourceQuoteCarriers: function (sourceCode) {
            return data.getSourceQuoteCarriers(sourceCode);
        },
        
        /**
         * Get source quote carrier shipping rates
         * 
         * @param {String} sourceCode
         * @param {String} carrierCode
         * @returns {Object[]}
         */
        getSourceQuoteCarrierShippingRates: function (sourceCode, carrierCode) {
            return data.getSourceQuoteCarrierShippingRates(sourceCode, carrierCode);
        },
        
        /**
         * Select shipping method
         * 
         * @param {String} sourceCode
         * @param {Object} shippingRate
         * @returns {Boolean}
         */
        selectShippingMethod: function (sourceCode, shippingRate) {
            data.setSourceQuoteShippingMethod(sourceCode, shippingRate.carrier_code + '_' + shippingRate.method_code);
            this.reloadSourceQuotes();
            return true;
        },
        
        /**
         * Get selected source shipping method
         * 
         * @param {String} sourceCode
         * @returns {String}
         */
        getSelectedSourceShippingMethod: function (sourceCode) {
            return data.getSourceQuoteShippingMethod(sourceCode);
        }
    });
});