jQuery(document).ready(function(b){if(b("#kurl_users").length>0){var c=b("#kurl_users").val();var a={};var d=[];b("#kusersearch").typeahead({source:function(e,f){b.ajax({url:c,cache:false,success:function(g){a={};d=[];b.each(g,function(h,i){d.push(i.name);a[i.name]=i;});f(d);}});},highlighter:function(f){var e=a[f];return'<div class="bond"><img src="'+e.photo+'" title="" /><br/><strong>'+e.name+"</strong></div>";}});}if(b("#kunena_search_results").is(":visible")){b("#search").collapse("hide");}});