define(['uiComponent','Anowave_TaxSwitch/js/switcher-data'], function (Component, switcherData) 
{
    'use strict';
    
    return Component.extend(
    {
        initialize: function () 
        {
            this._super();
            
            this.switcher = switcherData.bind(this).get('switcher');
        }
    });
});