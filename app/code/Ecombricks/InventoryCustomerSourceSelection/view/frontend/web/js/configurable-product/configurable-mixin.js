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
        
        $.widget('mage.configurable', target, {
            
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
             * Set options prices
             * 
             * @returns {Object}
             */
            setOptionsPrices: function () {
                var sourceCode = source.getValue();
                if (
                    sourceCode && 
                    this.options.jsonConfig && 
                    this.options.jsonConfig.sourceOptionPrices && 
                    sourceCode in this.options.jsonConfig.sourceOptionPrices
                ) {
                    this.options.jsonConfig.optionPrices = this.options.jsonConfig.sourceOptionPrices[sourceCode];
                }
                return this;
            },

            /**
             * Change source
             * 
             * @returns {Object}
             */
            changeSource: function () {
                this.setOptionsPrices();
                this._configureElement();
                return this;
            }
            
        });
        return $.mage.configurable;
    };
    
});