/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'Magento_Ui/js/form/element/single-checkbox'
], function (Field) {
    'use strict';

    return Field.extend({
        defaults: {
            optionName: '',
            isStockConfig: '0',
            manageStock: '',
            listens: {
                manageStock: 'onManageStockChange'
            },
            imports: {
                manageStock: '${ $.provider }:data.product.stock_data.manage_stock'
            },
            exports: {
                checked: '${ $.parentName }.${ $.optionName }:disabled'
            },
            modules: {
                option: '${ $.parentName }.${ $.optionName }'
            }
        },

        /**
         * Enable option
         * 
         * @returns {Object}
         */
        enableOption: function () {
            if (this.option()) {
                this.option().disabled(false);
            }
            return this;
        },

        /**
         * Disable option
         * 
         * @returns {Object}
         */
        disableOption: function () {
            if (this.option()) {
                this.option().disabled(true);
            }
            return this;
        },

        /**
         * Set default option value
         * 
         * @returns {Object}
         */
        setDefaultOptionValue: function () {
            if (this.option()) {
                this.option().value(this.option().default);
            }
            return this;
        },

        /**
         * @inheritdoc
         */
        onCheckedChanged: function (newChecked) {
            this._super(newChecked);
            if (newChecked) {
                this.setDefaultOptionValue();
            }
        },

        /**
         * Get initial value
         * 
         * @returns {String}
         */
        getInitialValue: function () {
            var values = [this.value(), this.default], value;
            values.some(function (v) {
                value = v || !!v;
                return value;
            });
            return this.normalizeData(value);
        },

        /**
         * On manage stock change
         */
        onManageStockChange: function () {
            if (this.isStockConfig === '1') {
                if (this.manageStock) {
                    this.disabled(false);
                    this.enableOption();
                } else {
                    this.disabled(true);
                    this.disableOption();
                }
            }
        }
    });
});