/**
 * Configurable variation left qty. UFUK
 */
define([
    'jquery',
    'underscore',
    'mage/url'
], function ($, _, urlBuilder) {
    'use strict';

    return function (productSku, salesChannel, salesChannelCode) {
		
        var selectorInfoStockSkuQty = '.availability.only',
            selectorInfoStockSkuQtyValue = '.availability.only > strong',
            productQtyInfoBlock = $(selectorInfoStockSkuQty),
            productQtyInfo = $(selectorInfoStockSkuQtyValue),
			productSource = $("[name='source']").val();
			

        if (!_.isUndefined(productSku) && productSku !== null) {
            $.ajax({
                url: urlBuilder.build('consignment/ajax/'),
                dataType: 'json',
                data: {
                    'sku': productSku,
                    'source': productSource
                }
            }).done(function (response) {
                if (response.qty !== null) {
                    productQtyInfo.text(response.qty);
                    productQtyInfoBlock.show();
                } else {
                    productQtyInfoBlock.hide();
                }
            }).fail(function () {
                productQtyInfoBlock.hide();
            });
        } else {
            productQtyInfoBlock.hide();
        }
    };
});
