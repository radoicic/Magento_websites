define(['jquery','ko'], function ($,ko) 
{
    'use strict';
    
    var buffer = 
    {
        data: {},
        bind: function (sectionName) 
        {
            this.data[sectionName] = ko.observable({});
        },
        get: function (sectionName) 
        {
            if (!this.data[sectionName]) 
            {
                this.bind(sectionName);
            }

            return this.data[sectionName];
        },
        notify: function (sectionName, sectionData) 
        {
            if (!this.data[sectionName]) 
            {
                this.bind(sectionName);
            }
            
            this.data[sectionName](sectionData);
        },
        update: function (sections) 
        {
        	$.each(sections, function(section, data)
        	{
        		buffer.notify(section, data);
        	});
        },
        remove: function (sections) {}
    };
    
    var switcherData = 
    {
    	options:
    	{
    		endpoint: null
    	},
    	bind: function(component)
    	{
    		$.getJSON(component.endpoint).done(function(sections)
    		{
    			buffer.update(sections);
    		});
    		
    		return this;
    	},
    	get: function(sectionName)
    	{
    		return buffer.get(sectionName);
    	}
    }

    return switcherData;
});
