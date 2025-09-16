define([
    'jquery',
    'vanillapicker',
    'fancyboxuby'
], function ($, Picker) {
    'use strict';

    return function (colorPickerContainer, targetSelector, cssAttribute, followingElementsWithAttributes) {
        const element = $(targetSelector);
        let color = element.css(cssAttribute);

        let picker = new Picker({
            parent: colorPickerContainer,
            popup: false, alpha: false, editor: true, color: color,
            onDone: function (color) {
                element.css(cssAttribute, color.rgbaString);
                followingElementsWithAttributes.forEach(([selector, cssAttribute]) => {
                    $(selector).css(cssAttribute, color.rgbaString);
                })
                $.fancyboxuby.close()
            },
        });

        $.fancyboxuby.open({
            'src': colorPickerContainer, 'type': 'inline', 'touch': false,
            'afterClose': () => picker.destroy()
        });
    }
});
