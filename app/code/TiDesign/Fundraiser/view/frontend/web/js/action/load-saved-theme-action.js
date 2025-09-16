define([
    'jquery',
    'mage/url',
    'TiDesign_Fundraiser/js/model/theme-storage',
    'TiDesign_Fundraiser/js/action/saved-theme-data-binding-action'
], function (
    $,
    urlBuilder,
    themeStorage,
    SavedThemeDataBindingAction
) {
    'use strict';

    return function () {
        return $.ajax({
            url: urlBuilder.build('fundraiser/form/loadSavedTheme'),
            data: {theme_id: themeStorage.FUNDRAISER_THEME_ID},
            showLoader: true,
            success: (response) => {
                const {theme_customer_config_data} = response;
                SavedThemeDataBindingAction(theme_customer_config_data);
            },
            error: error => {
                console.log(error);
            }
        });
    }
});
