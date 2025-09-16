/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'jquery',
    'Ecombricks_InventoryCustomerSourceSelection/js/catalog/product/source',
    'jquery-ui-modules/widget'
], function ($, source) {
    'use strict';
    
    return function (target) {
        
        $.widget('mage.priceBundle', target, {
            
            /**
             * Create
             * 
             * @returns {Object}
             */
            _create: function () {
                this._super();
                source.onChange(this.onSourceChange.bind(this));
                this.changeSource();
                return this;
            },
           
            /**
             * On source change
             * 
             * @returns {Object}
             */
            onSourceChange: function () {
                this.changeSource();
                return this;
            },
            
            /**
             * Set prices
             * 
             * @returns {Object}
             */
            setPrices: function () {
                var sourceCode = source.getValue();
                if (
                    sourceCode && 
                    this.options.jsonConfig && 
                    this.options.jsonConfig.sourceOptionPrices && 
                    sourceCode in this.options.jsonConfig.sourceOptionPrices
                ) {
                    this.options.optionConfig.prices = this.options.optionConfig.sourcePrices[sourceCode];
                }
                return this;
            },
           
            /**
             * Set options prices
             * 
             * @returns {Object}
             */
            setOptionsPrices: function () {
                if (!this.options.optionConfig.options) {
                    return this;
                }
                var sourceCode = source.getValue();
                $.each(this.options.optionConfig.options, function(optionId, optionConfig) {
                    $.each(optionConfig.selections, function(selectionId, selectionConfig) {
                        if (sourceCode in selectionConfig.sourcePrices) {
                            selectionConfig.prices = selectionConfig.sourcePrices[sourceCode];
                        }
                    });
                });
                return this;
            },
            
            /**
             * Change source
             * 
             * @returns {Object}
             */
            changeSource: function () {
                var options = $(this.options.productBundleSelector, this.element),
                    priceBox = $(this.options.priceBoxSelector, this.element);
                this.setPrices();
                this.setOptionsPrices();
                this.options.isOptionsInitialized = false;
                priceBox.trigger('price-box-initialized');
                options.trigger('change');
                return this;
            }
        });
        return $.mage.priceBundle;
    };
    
});