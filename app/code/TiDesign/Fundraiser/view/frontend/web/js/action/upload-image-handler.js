define([
    'jquery',
    'TiDesign_Fundraiser/js/action/upload-form-file-action',
    'TiDesign_Fundraiser/js/model/theme-storage',
    'TiDesign_Fundraiser/js/action/url/append-datetime-to-url',
    'fancyboxuby',
    'jquery-ui-modules/widget'
], function ($, UploadFormFileAction, ThemeStorage, AppendDateTimeToUrlAction) {
    'use strict';

    $.widget('mage.uploadImageHandler', {
        options: {
            targetSelector: '',
            fileUploadInputSelector: '',
            uploadFileButtonSelector: '',
            errorMessageSelector: '',
            previewSectionSelector: '',
            uploadFormSelector: '',
            uploadFormContainerSelector: '',

            maxFileSize: 2, // Megabyte unit
            allowedFileExtensions: ['jpg', 'jpeg', 'png'],
            uploadFileUrl: '',
        },
        fileUploadInputElement: null,
        uploadFileButtonElement: null,
        errorMessageElement: null,
        previewSectionElement: null,
        uploadFormElement: null,

        allowFileTypes: '',

        _create: function () {
            this._super();
            this._registerEvent()
            return this;
        },

        _registerEvent: function () {
            this.element.off('click.uploadImageHandler').on('click.uploadImageHandler', (e) => {
                e.preventDefault();

                this._bindElements();
                this._bindSelectImageEvent();
                this._bindUploadImageEvent();

                this._startUploadProgress();
            });
        },

        _bindElements: function () {
            this.fileUploadInputElement = $(this.options.fileUploadInputSelector);
            this.uploadFileButtonElement = $(this.options.uploadFileButtonSelector);
            this.errorMessageElement = $(this.options.errorMessageSelector);
            this.previewSectionElement = $(this.options.previewSectionSelector);
            this.uploadFormElement = $(this.options.uploadFormSelector);

            this.allowFileTypes = this.options.allowedFileExtensions.map(type => '.' + type).join(', ');
            this.uploadFormElement.find('.instruction-file-size').html(`Max file size: ${this.options.maxFileSize}Mb`);
            this.uploadFormElement.find('.instruction-file-type').html(`Allow file types: (${this.allowFileTypes})`);
        },

        _bindSelectImageEvent: function () {
            this.fileUploadInputElement.off('change.uploadImageHandler').on('change.uploadImageHandler', () => {
                this.uploadFileButtonElement.addClass("hide");
                this.errorMessageElement.empty();
                this.previewSectionElement.empty();

                const files = this.fileUploadInputElement[0].files;

                if (files && files[0]) {
                    const size = parseFloat(files[0].size) / 1024 / 1024;
                    const maxSize = this.options.maxFileSize;
                    if (size > maxSize) {
                        const message = `ERROR: Maximum upload file size is ${maxSize}Mb<br> your file size : ${size.toFixed(1)}Mb`;
                        this.errorMessageElement.html(message);
                        this.fileUploadInputElement.val('');
                    } else {
                        if (this._validateFileType()) {
                            this._previewImage();
                            this.uploadFileButtonElement.removeClass("hide");
                        }
                    }
                }
            });
        },

        _validateFileType: function () {
            let fileName = this.fileUploadInputElement.val();
            let idxDot = fileName.lastIndexOf(".") + 1;
            let extFile = fileName.substr(idxDot, fileName.length).toLowerCase();
            if (this.options.allowedFileExtensions.includes(extFile)) {
                return true;
            } else {
                this.errorMessageElement.html(
                    `ERROR: Only image files (${this.allowFileTypes}) files allowed`
                );
                this.fileUploadInputElement.val('');
                return false;
            }
        },

        _previewImage: function () {
            const files = this.fileUploadInputElement[0].files;
            if (files && files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.previewSectionElement.empty().append(
                        "<img class='responsive' src='" + e.target.result + "' alt>"
                    );
                };
                reader.readAsDataURL(files[0]);
            }
        },

        _bindUploadImageEvent: function () {
            this.uploadFileButtonElement.off('click.uploadImageHandler').on('click.uploadImageHandler', (e) => {
                e.preventDefault();

                const file = this.fileUploadInputElement[0].files[0];
                const form = new FormData(this.uploadFormElement[0]);
                form.append('filepath', file);
                form.append('theme_id', ThemeStorage.FUNDRAISER_THEME_ID);
                form.append('selector', this.options.targetSelector);

                UploadFormFileAction(form, this.options.uploadFileUrl).done((response) => {
                    const {success, image_url, message} = response;
                    if (success === true && image_url) {
                        $(this.options.targetSelector).attr("src", AppendDateTimeToUrlAction(image_url));
                        $.fancyboxuby.close()
                    } else {
                        this.errorMessageElement.html("ERROR:" + (message || 'Something went wrong!'));
                        this.fileUploadInputElement.val('');
                    }
                });
            });
        },

        _startUploadProgress: function () {
            $.fancyboxuby.open({
                'src': this.options.uploadFormContainerSelector, 'type': 'inline', 'touch': false,
                'afterLoad': () => {
                    const src = $(this.options.targetSelector).attr("src");
                    this.previewSectionElement.append(`<img class="responsive" src="${src}" />`);
                },
                'afterClose': () => {
                    this.previewSectionElement.empty();
                    this.fileUploadInputElement.val('');
                    this.errorMessageElement.empty();
                    this.uploadFileButtonElement.addClass("hide");
                }
            })
        }
    });
    return $.mage.uploadImageHandler;
});
