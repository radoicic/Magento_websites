require([
  'jquery',
  'fancyboxuby'
], function ($, fancyboxuby) {
    'use strict';
    $(document).ready(function(){
		$(document).on("click", '.tidesign-qty-details', function(event){
			event.cancelBubble = true;
			event.stopPropagation();
			
			
			var mModal 	= $(this).closest('tr').find('.tidesign-modal');
				$.fancyboxuby.open({ 
					'src' 		: mModal, 
					'type' 		: 'inline',
					'width'  : '800',
					'height' : '600',
					'max-width'  : '80%',
					'max-height' : '80%'
				});		
				
			if(mModal.data('rendered') != 1){	
			
				var WebData	= $.parseJSON($(this).closest('tr').find('.website-data').html());
				$.each( WebData, function( key, value ) {
					mModal.find('.data-list').append('<div class="list"><span class="title">'+ value +'</span><ul data-webid="'+key+'" class="animated-search-filter"></ul></div>');
				});
				
				var Sources = $.parseJSON($(this).closest('tr').find('.table-data').html());
				$.each( Sources, function( i, val ) {
					var id= val.website_id;
					if(id){
						mModal.find('.data-list').find('*[data-webid="'+ id +'"]').append('<li>'+ val.source_name + ' - '+ val.qty +'</li>');
					}
				});				


				mModal.data('rendered',1);
			}
			return false;
		});
		$(document).on("click", '.clear-search', function(event){
			$(this).parent().find('input.animated-search-filter').val("").focus().trigger('input');
			
		});
		$(document).on('input', 'input.animated-search-filter', function() {
			if($(this).val()!=""){
				$(".tidesign-modal.fancyboxuby-content .clear-search").show();
			}else{
				$(".tidesign-modal.fancyboxuby-content .clear-search").hide();
			}
			
			var texts = [];
			var i = -1
			var items = $(".tidesign-modal.fancyboxuby-content .animated-search-filter > *");
			var len = items.length;
			
			while (++i < len) {
				texts.push(items[i].textContent.trim())
				//items[i].style[transform] = "translateY(" + i*itemHeight +"px)"
			}	
			
			var re = new RegExp(this.value, "i");
			texts.forEach(function(element, index) {
				if (re.test(element)) {
					items[index].classList.remove("hidden");
				}else {
					items[index].classList.add("hidden")
				}
			})
		});
/*		
search
			var items = document.querySelectorAll(".animated-search-filter > *")
			var itemHeight = items[0].offsetHeight
			var texts = []
			var i = -1
			var len = items.length
			var transform = "transform" in document.body.style ? "transform" : "webkitTransform"

			while (++i < len) {
				texts.push(items[i].textContent.trim())
				items[i].style[transform] = "translateY(" + i*itemHeight +"px)"
			}

			document.querySelector("input.animated-search-filter").addEventListener("input", function() {
				var re = new RegExp(this.value, "i")
				texts.forEach(function(element, index) {
				
				if (re.test(element)) {
					items[index].classList.remove("hidden")
				}else {
					items[index].classList.add("hidden")
				}
				var i = -1
				var position = 0
				while (++i < len) {
					if (items[i].className != "hidden") {
						items[i].style[transform] = "translateY(" + position++ * itemHeight + "px)"
					}
				}
				})
			}) 

sort			
			$(".listitems").each(function(){
			  $(this).html($(this).children('li').sort(function(a, b){
				return ($(b).data('position')) < ($(a).data('position')) ? 1 : -1;
			  }));
			});			
						
			
*/
  
    });
});