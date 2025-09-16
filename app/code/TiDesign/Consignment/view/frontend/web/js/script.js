require(['jquery','mage/url'], function ($, urlBuilder) {
    'use strict';
    $(document).ready(function(){
		if($(".products.wrapper.products-list").hasClass("conscat")){
//			$(".breadcrumbs ul").append("<div class='conscat-list-download'><button class='download-stock'><i class='porto-icon-angle-double-down'></i> Download/Print Consignment Stock List</button></div>");
			$(".breadcrumbs ul").append('<div class="conscat-list-download"><div class="download-container"><button id="dropdownMenuButton" class="download-stock2 "><i class="porto-icon-angle-double-down"></i> Download/Print Consignment Stock List</button><ul id="download-menu" aria-labelledby="dropdownMenuButton" class="dropdown-menu"><li><a id="xml_download" class="dropdown-item" href="#">Download .XML</a></li><li><a id="xml_download_p" class="dropdown-item" href="#">Download .XML with price</a></li></ul></div></div>');
		}
		


		$('#dropdownMenuButton').click(function(){
			$('.dropdown-menu').toggleClass('show');
			$('#dropdownMenuButton').toggleClass('open')
		})
		
		$(document).on("click", '#xml_download', function(event){
			var url = urlBuilder.build('/consignment/ajax/download')
            window.location.href = url;
		});		
		
		$(document).on("click", '#xml_download_p', function(event){
			var url = urlBuilder.build('/consignment/ajax/downloadp')
            window.location.href = url;
		});
		
		$(document).on("mouseup", function(e) {
			var container = $(".conscat-list-download");
			if (!container.is(e.target) && container.has(e.target).length === 0){
				$('.dropdown-menu').removeClass('show');
				$('#dropdownMenuButton').removeClass('open');				
			}
		});
	});
});
