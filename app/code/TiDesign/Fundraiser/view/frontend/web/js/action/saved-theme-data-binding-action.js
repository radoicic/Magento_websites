define([
    'jquery',
    'TiDesign_Fundraiser/js/model/theme-storage',
    'TiDesign_Fundraiser/js/action/url/append-datetime-to-url'
], function ($, themeStorage, AppendDateTimeToUrlAction) {
    'use strict';

    function bindColorPicker(config, value) {
        const {target_selector, css_attribute} = config;
        const element = $(target_selector);
        return element.css(css_attribute, value);
    }

    function bindImageUploader(config, value) {
        const {target_selector} = config;
        $(target_selector).attr("src", AppendDateTimeToUrlAction(value));
    }

    function bindTextEditor(config, value) {
        const {target_selectors} = config;
        $(target_selectors).each((index, el) => {
            if (value[index] !== undefined) {
                el.innerHTML = value[index];
            }
        })
    }

    return function (customerConfigData) {
        const {editors} = JSON.parse(themeStorage.FUNDRAISER_THEME_CONFIG_DATA);
        customerConfigData = customerConfigData ? JSON.parse(customerConfigData) : {};
        Object.keys(customerConfigData).forEach(key => {
            const value = customerConfigData[key];
            const config = editors[key];
            switch (config.type) {
                case "color_picker":
                    return bindColorPicker(config, value);
                case 'image_uploader':
                    return bindImageUploader(config, value);
                case 'text_editor':
                    return bindTextEditor(config, value);
            }
        });
    }
})
