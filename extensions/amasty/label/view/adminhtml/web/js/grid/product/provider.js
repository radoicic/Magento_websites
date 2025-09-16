define([
    'jquery',
    'Magento_Ui/js/grid/provider'
], function ($, provider) {
    'use strict';

    return provider.extend({

        // eslint-disable-next-line no-unused-vars
        reload: function (options) {
            var labelRuleCondition = $('[data-form-part="amasty_labels_conditions"]').serialize();

            if (typeof this.params.filters === 'undefined') {
                this.params.filters = {};
            }

            this.params.filters.label_rule_condition = labelRuleCondition;

            this._super({'refresh': true});
        }
    });
});
