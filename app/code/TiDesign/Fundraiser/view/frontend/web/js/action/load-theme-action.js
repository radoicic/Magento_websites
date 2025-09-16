define([
    'jquery',
    'mage/url',
    'TiDesign_Fundraiser/js/model/theme-storage',
    'TiDesign_Fundraiser/js/model/leader-line-creator',
    'TiDesign_Fundraiser/js/action/saved-theme-data-binding-action',
    'TiDesign_Fundraiser/js/action/color-picker-handler',
    'TiDesign_Fundraiser/js/action/upload-image-handler',
    'TiDesign_Fundraiser/js/action/text-editor-handler',
    'waitforimages'
], function (
    $,
    urlBuilder,
    themeStorage,
    LeaderLineCreator,
    SavedThemeDataBindingAction
) {
    'use strict';

    function setupThemeConfigData(configData) {
        const {anchors, editors} = JSON.parse(configData);
        setupAnchors(anchors);
        setupEditors(editors);
    }

    function setupAnchors(anchors) {
        themeStorage.ANCHOR_INSTANCES.forEach(anchor => anchor.remove());
        themeStorage.ANCHOR_INSTANCES = [];
        anchors.forEach(config => {
            const {selector, type, target_selector} = config;
            const typeCapitalized = type.split(' ').map(str => str.charAt(0).toUpperCase() + str.slice(1));
            const anchorFunction = ['create', typeCapitalized.join(''), 'Anchor'].join('');
            if (LeaderLineCreator.hasOwnProperty(anchorFunction)) {
                const anchor = LeaderLineCreator[anchorFunction](selector, target_selector);
                themeStorage.ANCHOR_INSTANCES.push(anchor);
            }
        });
    }

    function setupEditors(editors) {
        Object.keys(editors).forEach(key => {
            const config = editors[key];
            switch (config.type) {
                case "color_picker":
                    return setupColorPicker(config);
                case 'image_uploader':
                    return setupImageUploader(config);
                case 'text_editor':
                    return setupTextEditor(config);
            }
        });
    }

    function setupColorPicker(config) {
        const followingSelectors = config.following_selectors || [];
        const followingSelectorsConfig = followingSelectors.map(item => [item.target_selector, item.css_attribute]);
        const options = {
            colorPickerContainerSelector: '#colorPickerContainer',
            targetSelector: config.target_selector,
            cssAttribute: config.css_attribute,
            followingSelectorsWithAttributes: followingSelectorsConfig
        }
        $(config.selector).colorPickerHandler(options);
    }

    function setupImageUploader(config) {
        const options = {
            fileUploadInputSelector: '#fileAjax',
            uploadFileButtonSelector: '#upload_file_button',
            errorMessageSelector: '#upload_error',
            previewSectionSelector: '.uploadPreview',
            uploadFormSelector: '#formAjax',
            uploadFormContainerSelector: '#fileUpload-form',
            targetSelector: config.target_selector,

            maxFileSize: config.max_file_size,
            allowedFileExtensions: config.allow_file_extensions,
            uploadFileUrl: urlBuilder.build('fundraiser/ajax/upload')
        };
        $(config.selector).uploadImageHandler(options);
    }

    function setupTextEditor(config) {
        const options = {
            textEditorContainerSelector: '#textEditorContainer',
            textEditorPlaceholderSelector: '#textEditorContainer .textEditorPlaceholder',
            changeTextButtonSelector: '#textEditorContainer #changeTextButton',
            targetElementSelectors: config.target_selectors,
            attributes: config.attributes
        };
        $(config.selector).textEditorHandler(options);
    }

    return function (themeId, isLoadSaved) {
        $("#theme_selector").hide();
        const data = {theme_id: themeId || themeStorage.FUNDRAISER_THEME_ID};
        if (isLoadSaved) {
            data.is_load_saved = true;
        }
        $.ajax({
            url: urlBuilder.build('fundraiser/form/loadTheme'),
            data: data,
            showLoader: true,
            success: (response) => {
                const {theme_id, html, config_data, theme_customer_config_data} = response;
                themeStorage.FUNDRAISER_THEME_ID = theme_id;
                themeStorage.FUNDRAISER_THEME_CONFIG_DATA = config_data;
                $("#page_theme").html(html);
                $('#page_theme').waitForImages().done(function () {
                    $("#theme_customise").fadeIn();
                    setupThemeConfigData(config_data);
                    SavedThemeDataBindingAction(theme_customer_config_data);
                });
            },
            error: error => {
                console.log(error);
                $("#theme_selector").show();
            }
        });
    }
});
