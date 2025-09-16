define([
    'jquery',
    'jquery-ui-modules/widget',
    'fancyboxuby'
], function ($) {
    'use strict';

    $.widget('mage.textEditorHandler', {
        options: {
            textEditorContainerSelector: '',
            targetElementSelectors: '',
            attributes: [],

            textEditorPlaceholderSelector: '',
            changeTextButtonSelector: '',
        },
        renderedElements: [],


        _create: function () {
            this._super();
            this._registerEvent();
            return this;
        },

        _registerEvent: function () {
            this.element.off('click.textEditorHandler').on('click.textEditorHandler', (e) => {
                e.preventDefault();

                this._renderElements();

                this._bindChangeTextEvent();

                $.fancyboxuby.open({
                    'src': this.options.textEditorContainerSelector,
                    'type': 'inline',
                    'touch': false,
                    'afterClose': () => this._removeRenderedElements()
                });
            })
        },

        _renderElements: function () {
            this.renderedElements = [];
            const placeHolder = document.querySelector(this.options.textEditorPlaceholderSelector);

            const elements = $(this.options.targetElementSelectors);
            const attributes = this.options.attributes;
            elements.each((index, element) => {
                const text = this._formatText(element.innerHTML) || '';
                const input = document.createElement("input");
                input.type = 'text';
                input.name = `text-editor-input-${index}`;
                input.value = text;
                const elementAttributes = attributes && attributes[index] || [];
                Object.keys(elementAttributes).forEach((code) => {
                    const attributeValue = [elementAttributes[code], input.getAttribute(code) || ''];
                    input.setAttribute(code, attributeValue.filter(value => value !== '').join(' '));
                });
                placeHolder.append(input);
                this.renderedElements.push(input);
            });
        },

        _bindChangeTextEvent: function () {
            $(this.options.changeTextButtonSelector)
                .off('click.textEditorHandler')
                .on('click.textEditorHandler', (e) => {
                    e.preventDefault();
                    const targetElements = $(this.options.targetElementSelectors);
                    this.renderedElements.forEach((el, index) => {
                        targetElements.get(index).innerHTML = this._formatText(el.value);
                    });
                    $.fancyboxuby.close();
                });
        },

        _removeRenderedElements: function () {
            this.renderedElements.forEach(el => el.remove());
        },

        _formatText: function (str, replaceMode) {
            return str.replace(/<\s*\/?br\s*[\/]?>/gi, "\r");
        }
    });

    return $.mage.textEditorHandler;
})
