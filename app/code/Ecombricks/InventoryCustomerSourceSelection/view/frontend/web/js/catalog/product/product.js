/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'jquery',
    'Ecombricks_Common/js/jquery-serialize-json'
], function ($) {
    'use strict';
    
    var formElement = $('.product-add-form form').get(0);
    
    return {
        
        /**
         * Get form
         * 
         * @returns {jQuery}
         */
        getForm: function () {
            return $(formElement);
        },
        
        /**
         * Get JSON
         * 
         * @returns {Object}
         */
        getJson: function () {
            return $(formElement).serializeJson();
        },
        
        /**
         * Check if is valid
         * 
         * @returns {Boolean}
         */
        isValid: function () {
            return $(formElement).valid();
        }
        
    };
});