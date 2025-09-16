define([
    'jquery',
    'TiDesign_Fundraiser/js/action/color-picker-executor',
    'jquery-ui-modules/widget'
], function ($, ColorPickerExecutor) {
    'use strict';

    $.widget('mage.colorPickerHandler', {
        options: {
            colorPickerContainerSelector: '',
            targetSelector: '',
            cssAttribute: '',

            followingSelectorsWithAttributes: []
        },


        _create: function () {
            this._super();

            this.element.off('click.colorPickerHandler').on('click.colorPickerHandler', (event) => {
                event.preventDefault();
                const colorPickerContainer = document.querySelector(this.options.colorPickerContainerSelector);
                ColorPickerExecutor(
                    colorPickerContainer,
                    this.options.targetSelector,
                    this.options.cssAttribute,
                    this.options.followingSelectorsWithAttributes
                )
            })
            return this;
        },
    });

    return $.mage.colorPickerHandler;
})
