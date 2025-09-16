/* global toggleValueElements */
/* eslint-disable no-native-reassign */
define([
    'jquery',
    'knockout',
    'Swissup_CheckoutSuccess/lib/dragula/dragula',
    'mage/utils/wrapper'
], function ($, ko, dragula, wrapper) {
    'use strict';

    /**
     * KO view model to maintain layout mockup
     *
     * @param {Object} configValue
     * @param {Object} availableBlocks
     */
    function DragulaElementViewModel(configValue, availableBlocks) {
        var self = this;

        self.availableBlocks = availableBlocks;

        self.containers = $.map(JSON.parse(configValue), function (element) {
            element.children = ko.observableArray(element.children);

            return element;
        });

        /**
         * [removeChild description]
         */
        self.removeChild = function (block, container) {
            container.children.remove(block);
        };

        /**
         * [addChild description]
         */
        self.addChild = function (block, container, beforeBlock) {
            var index;

            if (beforeBlock) {
                index = container.children().indexOf(beforeBlock);
                container.children.splice(index, 0, block);
            } else {
                container.children.push(block);
            }
        };

        self.layoutConfigValue = ko.computed(function () {
            return ko.toJSON(self.containers);
        });

        /**
         * @param  {HTMLElement} el
         * @param  {HTMLElement} target
         * @param  {HTMLElement} source
         * @param  {HTMLElement} sibling
         */
        self.onDragulaDrop = function (el, target, source, sibling) {
            var block;

            if (!target) {
                return;
            }

            if ($(source).hasClass('copy-only')) {
                // create new ko data block
                block = {
                    'name': $(el).data('name')
                };
            } else {
                // remove block from old container
                block = ko.dataFor(el);
                self.removeChild(block, ko.dataFor(source));
            }

            // add block to new container
            self.addChild(
                block,
                ko.dataFor(target),
                sibling ? ko.dataFor(sibling) : null
            );
            // remove droped element to prevent block duplication
            $(el).remove();
        };
    }

    /**
     * Wrap toggle function to enable/disable dragula element.
     */
    function _wrapToggleValueElements() {
        toggleValueElements = wrapper.wrap(
            toggleValueElements,
            function () {
                var args = Array.prototype.slice.call(arguments),
                    originalFn = args.shift();

                $(args[1]).find('.dragula-element').toggleClass('disabled');

                return originalFn.apply(this, args);
            }
        );
    }

    return function (options, element) {
        const $el = $(element),
            $textarea = $el.children('textarea');

        var dragulaVM;

        $textarea.attr('data-bind', 'value: layoutConfigValue').hide();
        $('<div data-bind="template: { name: \'layoutTemplate\' }"></div>').insertAfter($textarea);

        // apply knockout binding
        dragulaVM = new DragulaElementViewModel(
            $textarea.val(),
            options.availableBlocks
        );

        dragulaVM.disabled = options.disabled;
        dragulaVM.afterRender = function () {
            // initialize dragula
            dragula(
                $(`#${options.parentId} .dragula-element`).children().toArray(),
                {
                    /**
                     * listener when dragula element copied
                     */
                    copy: function (el, source) {
                        return $(source).hasClass('copy-only');
                    },

                    /**
                     * listener when dragula element accepts new element
                     */
                    accepts: function (el, target) {
                        return !$(target).hasClass('copy-only');
                    }
                }
            ).on('drop', dragulaVM.onDragulaDrop);

            // hide spinner
            $el.children('[data-role="spinner"]').hide();
        };

        ko.cleanNode($el.get(0));
        ko.applyBindings(dragulaVM, $el.get(0));

        _wrapToggleValueElements();
    };
});
