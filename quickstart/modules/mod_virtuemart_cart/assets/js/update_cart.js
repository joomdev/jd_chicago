if (typeof Virtuemart === "undefined")
	Virtuemart = {};

jQuery(function($) {
	Virtuemart.customUpdateVirtueMartCartModule = function(el, options){
		var base 	= this;
		base.el 	= jQuery(".vmCartModule");
		base.options 	= jQuery.extend({}, Virtuemart.customUpdateVirtueMartCartModule.defaults, options);
			
		base.init = function(){
			jQuery.ajaxSetup({ cache: false })
			jQuery.getJSON(Virtuemart.vmSiteurl + "index.php?option=com_virtuemart&nosef=1&view=cart&task=viewJS&format=json" + Virtuemart.vmLang,
				function (datas, textStatus) {
					base.el.each(function( index ,  module ) {
						if (datas.totalProduct > 0) {
							jQuery(module).find(".vm_cart_products").html("");
							jQuery.each(datas.products, function (key, val) {
								//jQuery("#hiddencontainer .vmcontainer").clone().appendTo(".vmcontainer .vm_cart_products");
								jQuery(module).find(".hiddencontainer .vmcontainer .product_row").clone().appendTo( jQuery(module).find(".vm_cart_products") );
								jQuery.each(val, function (key, val) {
									jQuery(module).find(".vm_cart_products ." + key).last().html(val);
								});
							});
						}
						jQuery(module).find(".show_cart").html(		datas.cart_show);
						jQuery(module).find(".total_products").html(	datas.totalProductTxt);
						jQuery(module).find(".total").html(		datas.billTotal);
					});
				}
			);			
		};
		base.init();
	};
	// Definition Of Defaults
	Virtuemart.customUpdateVirtueMartCartModule.defaults = {
		name1: 'value1'
	};

});

jQuery(document).ready(function( $ ) {
	jQuery(document).off("updateVirtueMartCartModule","body",Virtuemart.customUpdateVirtueMartCartModule);
	jQuery(document).on("updateVirtueMartCartModule","body",Virtuemart.customUpdateVirtueMartCartModule);
});
