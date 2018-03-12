/**
 * vmsite.js: General Javascript Library for VirtueMart Frontpage
 *
 *
 * @package	VirtueMart
 * @subpackage Javascript Library
 * @author Patrick Kohl
 * @author Max Milbers
 * @copyright Copyright (c) 2014 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

if (typeof Virtuemart === "undefined")
	Virtuemart = {};
(function($){
	var undefined,
	methods = {
		list: function(options) {
			var dest = options.dest;
			var ids = options.ids;
			var prefix = options.prefiks;
            methods.update(this,dest,ids,options.prefiks);
			jQuery(this).change( function() { methods.update(this,dest,ids,options.prefiks)});
		},
		update: function(org,dest,ids,prefix) {
			var opt = jQuery(org),
				optValues = opt.val() || [],
				byAjax = [] ;
			if (!jQuery.isArray(optValues)) optValues = jQuery.makeArray(optValues);
			if ( typeof  oldValues === "undefined") {
				oldValues = [];
			}
			if ( typeof  oldValues[prefix] !== "undefined") {

				//remove if not in optValues
				jQuery.each(oldValues[prefix], function(key, oldValue) {
				var sel = "#"+prefix+"group"+oldValue;
					console.log('remove old values',sel, oldValue);
					//if ( (jQuery.inArray( oldValue, optValues )) < 0 )
					jQuery(sel).remove();
				});
			}
			//push in 'byAjax' values and do it in ajax
			jQuery.each(optValues, function(optkey, optValue) {
				if( opt.data( 'd'+optValue) === undefined ) byAjax.push( optValue );
			});

			if (byAjax.length >0) {
				jQuery.getJSON(Virtuemart.vmSiteurl + 'index.php?option=com_virtuemart&view=state&format=json&virtuemart_country_id=' + byAjax,
						function(result){

						var virtuemart_state_id = jQuery('#'+prefix+'virtuemart_state_id_field');
						var status = virtuemart_state_id.attr('required');

                        jQuery.each(result, function(key, value) {
							if (value.length >0) {
								opt.data( 'd'+key, value );	
							} else { 
								opt.data( 'd'+key, 0 );		
							}
						});
						methods.addToList(opt,optValues,dest,prefix);
						if ( typeof  ids !== "undefined") {
							var states =  ids.length ? ids.split(',') : [] ;
							jQuery.each(states, function(k,id) {
								jQuery(dest).find('[value='+id+']').attr("selected","selected");
							});
						}
                        jQuery(dest).trigger("liszt:updated"); //in new chosen this is chosen:updated
					}
				);
			} else {
				methods.addToList(opt,optValues,dest,prefix)
				jQuery(dest).trigger("liszt:updated");
			}
			oldValues[prefix] = optValues ;
			
		},
		addToList: function(opt,values,dest,prefix) {
			jQuery.each(values, function(dataKey, dataValue) {
				var groupExist = $("#"+prefix+"group"+dataValue+"").size();
				if ( ! groupExist ) {
					var datas = opt.data( 'd'+dataValue );
					if (datas.length >0) {
						var label = opt.find("option[value='"+dataValue+"']").text();
						var group ='<optgroup id="'+prefix+'group'+dataValue+'" label="'+label+'">';
						jQuery.each( datas  , function( key, value) {
							if (value) group +='<option value="'+ value.virtuemart_state_id +'">'+ value.state_name +'</option>';
						});
						group += '</optgroup>';
						jQuery(dest).append(group);
					}
				}
			});
		},
        startVmLoading: function(msg) {
            if (msg===undefined) {
                msg='';
            }
            var tmp = new Object();
            tmp.data = new Object();
			tmp.data.msg===msg;
            Virtuemart.startVmLoading(tmp);
        },
        stopVmLoading: function() {
			Virtuemart.stopVmLoading();
        }

	};

	Virtuemart.startVmLoading = function(e) {
		var msg='';
		if (e.data.msg!==undefined) {
			msg = e.data.msg;
		}
		jQuery("body").addClass("vmLoading");
		//jQuery("body").fadeIn( 400 );
		jQuery("body").append("<div class=\"vmLoadingDiv\"><div class=\"vmLoadingDivMsg\">"+msg+"</div></div>");
	};

	Virtuemart.stopVmLoading = function() {
		if( jQuery("body").hasClass("vmLoading") ){
			jQuery("body").removeClass("vmLoading");
			jQuery('div.vmLoadingDiv').remove();
		}
	};

	$.fn.vm2front = function( method ) {
		if ( methods[method] ) {
		  return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
			jQuery.error( 'Method ' +  method + ' does not exist on Vm2 front jQuery library' );
		}    
	
	};
})(jQuery)
