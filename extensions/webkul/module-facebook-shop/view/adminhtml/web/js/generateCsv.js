/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_FacebookShop
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
define([
    "jquery",
    "jquery/ui"
], function ($) {
    "use strict";
    $.widget('facebookshop.generateCsv', {
        _create: function () {
            var options = this.options;
            $("#generate_csv_button").on("click", function () {
                var alerttext = '';
                new Ajax.Request(options.generateCsvUrl, {
                    method: 'post',
                    parameters: {
                        'initiatedBy': 'Backend Admin'
                    },
                    onSuccess: function (transport) {
                        var response = $.parseJSON(transport.responseText);
                        if (response.msg) {
                            $('<div />').html(response.msg)
                                .modal({
                                    title: $.mage.__('Attention'),
                                    autoOpen: true,
                                    buttons: [{
                                     text: 'OK',
                                        attr: {
                                            'data-action': 'cancel'
                                        },
                                        'class': 'action-primary',
                                        click: function () {
                                                this.closeModal();
                                            }
                                    }]
                                 });
                        } else {
                            $('<div />').html(alerttext)
                                .modal({
                                    title: $.mage.__('Attention'),
                                    autoOpen: true,
                                    buttons: [{
                                     text: 'OK',
                                        attr: {
                                            'data-action': 'cancel'
                                        },
                                        'class': 'action-primary',
                                        click: function () {
                                                this.closeModal();
                                            }
                                    }]
                            });
                        }
                    }
                });
            });
        }
    });
    return $.facebookshop.generateCsv;
});
