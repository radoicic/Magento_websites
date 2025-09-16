jQuery(document).ready(function($) {

/******************************************	search     ******************************************/
	let t;$("#search").on("input propertychange",(function(){let e=$(this);clearTimeout(t),$("#search_autocomplete").html(""),e.val().length>=3&&(t=setTimeout((function(){let t=e.val();$.getJSON("https://www.nzboxer.com/search/ajax/suggest/",{q:t},$.proxy((function(t){if(t.length){let e="";$.each(t,(function(t,o){e+='<li id="qs-option-'+t+'"  role="option"><span class="qs-option-name">'+o.title+'</span><span aria-hidden="true" class="amount">'+o.num_results+"</span></li>"})),e='<ul role="listbox">'+e+"</ul>",$("#search_autocomplete").html(e)}else console.log("yok")}),this))}),1500))})),$("body").on("click","#search_autocomplete ul li",(function(){$("#search_mini_form").trigger("submit")}));
	
	/** todo
		enter search
		escape dropclose
	**/
	
	
/******************************************	search end ******************************************/

/******************************************	cart     ******************************************/
	let summary_count, subtotalAmount = 0;
	let item_template, item_list;
	let c_wrap = $('#minicart-content-wrapper');
/*
	$.ajaxSetup({async:false});
	$.get('assets/cart/data.php').done(function(data) {
		item_template = JSON.parse(data);
		summary_count 	= item_template.summary_count;
		subtotalAmount	= item_template.subtotalAmount;
		item_list		= '<div class="minicart-items-wrapper"><ol id="mini-cart" class="minicart-items">'+item_template.items+'</ol></div>';
	});
	$.ajaxSetup({async:true});
	
	let c_button = '<div class="actions"><button id="top-cart-btn-checkout" type="button" class="action primary checkout" title="Go to Checkout">Go to Checkout</button></div>';
	let c_empty	 = '<strong class="subtitle empty">You have no items in your shopping cart.</strong>';
	let c_total  = '<div class="subtotal"><span class="label"><span>Subtotal</span></span><div class="amount price-container"><span class="price-wrapper"><span class="price">'+ 'NZ$'+subtotalAmount+'</span></span></div></div>';

	$('.minicart-wrapper .counter-number, .mcw-header .count').html(summary_count);
	$('.mcw-header .counter-label').html(summary_count >1? 'Items':'Item' );
	c_wrap.append( ((summary_count===0) ? c_empty :  item_list + c_total) + c_button);
	
	
	$(".action.showcart").on("click",function(event){	event.preventDefault();	event.stopPropagation();$(this).parent().toggleClass("active")});
	$(document).on('click',function (event){
		if($(event.target).parents('.minicart-wrapper').length > 0) { return;}
		$('.minicart-wrapper.active').removeClass('active');
	});
	$(document).on('click','#top-cart-btn-checkout',function(){window.location.href='https://www.nzboxer.com/checkout/cart/';})
	$(document).on('click','.minicart-items .product .product.options > .toggle',function (event){
		$(this).parent().toggleClass("active")
	});
	$(document).on("change paste keyup", '.cart-item-qty', function() {
		if($(this).val() != $(this).data('item-qty')){
			$('#update-cart-item-'+ $(this).data('cart-item')).show();
		}else{
			$('#update-cart-item-'+ $(this).data('cart-item')).hide();
		}
	});
	
	const dialog = document.querySelector("dialog");
	let d_id;
	$(document).on('click','.minicart-wrapper .product .actions > .secondary a.action.delete', function(){
		dialog.showModal();
		d_id = $(this).data("cart-item");
		console.log(d_id);
	});
	$(document).on('click',"dialog .action-dismiss", function(){
		dialog.close();
		console.log("kapat");
	});
	$(document).on('click',"dialog .action-accept", function(){
		console.log(d_id);
		dialog.close();
		console.log("kapat");
	});
/******************************************	cart end ******************************************/

});