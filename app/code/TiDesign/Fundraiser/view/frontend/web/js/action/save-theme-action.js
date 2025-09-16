define([
    'jquery',
    'mage/url',
    'TiDesign_Fundraiser/js/model/theme-storage',
], function (
    $,
    urlBuilder,
    themeStorage
) {
    'use strict';

    function collectThemeData() {
        const configData = themeStorage.FUNDRAISER_THEME_CONFIG_DATA;
        const {editors} = JSON.parse(configData);
        const result = {};
        Object.keys(editors).forEach(key => {
            const config = editors[key];
            switch (config.type) {
                case "color_picker":
                    result[key] = getColor(config);
                    return;
                case 'text_editor':
                    result[key] = getTexts(config);
                    return;
            }
        });
        return result;
    }

    function getColor(config) {
        const {target_selector, css_attribute} = config;
        const element = $(target_selector);
        return element.css(css_attribute);
    }

    function getTexts(config) {
        const {target_selectors} = config;
        return $.map($(target_selectors), element => element.innerHTML);
    }

    return function (inform) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: urlBuilder.build('fundraiser/form/saveTheme'),
                data: {
                    theme_id: themeStorage.FUNDRAISER_THEME_ID,
                    data: collectThemeData()
                },
                method: 'POST',
                showLoader: true,
                success: (data) => {
                    inform && alert('Successfully.');
                    resolve(data);
                },
                error: error => {
                    reject(error);
                }
            });
        })
    }
});
