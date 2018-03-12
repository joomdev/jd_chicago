<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Actions\Html;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
Class Html extends \GCore\Admin\Extensions\Chronoforms\Action{
	static $title = 'HTML (Render Form)';
	var $defaults = array(
		'submit_event' => 'submit',
		'add_form_tags' => 1,
		'page' => 1,
		'xhtml_url' => 0,
	);

	function execute(&$form, $action_id){
		$config = !empty($form->actions_config[$action_id]) ? $form->actions_config[$action_id] : array();
		$config = new \GCore\Libs\Parameter($config);

		$doc = \GCore\Libs\Document::getInstance();
		$form_id = 'chronoform-'.$form->form['Form']['title'];
		//$doc->_('forms');

		//check fields validation
		/*
		if(!empty($form->form['Form']['extras']['fields'])){
			$validations = array();
			foreach($form->form['Form']['extras']['fields'] as $k => $field){
				if(!empty($field['validation'])){
					foreach($field['validation'] as $rule => $rule_data){
						$validations[$rule][] = $field['name'].(strlen(trim($rule_data)) > 0 ? ':'.$rule_data : ':');
					}
				}
				if(!empty($field['inputs'])){
					foreach($field['inputs'] as $fn => $field_input){
						if(!empty($field_input['validation'])){
							foreach($field_input['validation'] as $rule => $rule_data){
								$validations[$rule][] = $field_input['name'].(strlen(trim($rule_data)) > 0 ? ':'.$rule_data : ':');
							}
						}
					}
				}
			}
			foreach($validations as $rule => &$fields){
				$fields = implode("\n", $fields);
			}
			$form->execute('client_validation', array('rules' => $validations));
		}
		*/
		$theme = $form->params->get('theme', 'bootstrap3');
		/*if($form->params->get('theme', 'bootstrap3') == 'bootstrap3'){
			$theme = 'bootstrap3';
		}else if($form->params->get('theme', 'bootstrap3') == 'bootstrap3_pure'){
			$theme = 'bootstrap3_pure';
		}else if($form->params->get('theme', 'bootstrap3') == 'semantic1'){
			$theme = 'semantic1';
		}else if($form->params->get('theme', 'bootstrap3') == 'gcoreui'){
			$theme = 'gcoreui';
		}else if($form->params->get('theme', 'bootstrap3') == 'none'){
			$theme = 'none';
		}*/
		$doc->theme = $theme;
		\GCore\Helpers\Theme::getInstance();
		if($form->params->get('tight_layout', 0)){
			$doc->addCssCode('
				.gbs3 .gcore-form-row{margin-bottom:5px;}
				.gcore-form-row .gcore-line-td{margin:0;}
			');
		}
		if($form->params->get('rtl_support', 0)){
			$doc->addCssCode('
				#'.$form_id.'.chronoform{direction:rtl;}
			');
			if($form->params->get('theme', 'bootstrap3') == 'bootstrap3'){
				$doc->addCssCode('
					#'.$form_id.' .gcore-label-left{
						float:right !important;
						min-width:160px;
						max-width:160px;
						padding-left:7px;
						text-align:right !important;
					}
					#'.$form_id.' .gcore-label-top{
						display:block;
						text-align:right !important;
						float:none !important;
						width:auto !important;
					}
					#'.$form_id.' .gcore-form-row > .gcore-label-checkbox{
						float:right !important;
						min-width:160px;
						padding-right:7px;
						padding-top: 1px !important;
						text-align:right !important;
					}
					#'.$form_id.' .gcore-subinput-container{
						float:right;
					}
					#'.$form_id.' .gcore-multiple-column .gcore-checkbox-item, .gcore-multiple-column .gcore-radio-item{
						float: right;
					}
					#'.$form_id.' .gcore-multiple-column .gcore-checkbox-item:not(:first-child), .gcore-multiple-column .gcore-radio-item:not(:first-child){
						padding-right: 5px;
					}
				');
			}
			if($form->params->get('theme', 'bootstrap3') == 'gcoreui'){
				$doc->addCssCode('
					#'.$form_id.' .gcore-label-left {
						min-width: 150px !important;
						max-width: 150px !important;
						display: inline-block;
						white-space: normal;
						float: right !important;
						padding: 1px;
						padding-right: 10px !important;
						font-weight: bold;
					}
					#'.$form_id.' .gcore-input-container {
						float: right;
						overflow: auto;
						display: inline-block;
						white-space: normal;
					}
					#'.$form_id.' .gcore-line-tr .gcore-input{
						float:right;
					}
					#'.$form_id.' .gcore-subinput-container {
						margin-bottom: 3px;
						overflow: auto;
						float: right;
					}
					#'.$form_id.' .gcore-subinput-container:not(:first-child) {
						padding-right: 4px;
					}
					#'.$form_id.' .gcore-subinput-container-wide {
						display: inline-block;
						margin: 0px 3px 3px 0px;
						float: right;
						overflow: auto;
					}
					#'.$form_id.' .gcore-radio-item,
					#'.$form_id.' .gcore-checkbox-item {
						float: right;
						margin: 0px 6px 6px 0px;
						white-space: nowrap;
					}
					#'.$form_id.' .gcore-single-column .gcore-radio-item,
					#'.$form_id.' .gcore-single-column .gcore-checkbox-item {
						clear: right;
					}
				');
			}
		}
		if($form->params->get('labels_right_aligned', 0)){
			$doc->addCssCode('
				#'.$form_id.' .gcore-label-left{
					text-align:'.($form->params->get('rtl_support', 0) ? 'left' : 'right').' !important;
				}
			');
		}
		if($form->params->get('labels_auto_width', 0)){
			$doc->addCssCode('
				#'.$form_id.' .gcore-label-left{
					min-width: 0px !important;
					max-width: none !important;
					width: auto !important;
				}
			');
		}
		
		
		if($form->params->get('js_validation_language', '') == ''){
			$lang = strtolower(\GCore\Libs\Base::getConfig('site_language'));
			$js_lang_tag = explode('-', $lang);
			$form->params->set('js_validation_language', $js_lang_tag[0]);
		}
		
		$events_codes = array();
		//check fields events
		if(!empty($form->form['Form']['extras']['fields'])){
			//$events_codes = array();
			$pageload_events_codes = array();
			//$events_codes[] = 'jQuery(document).ready(function($){';
			$events_codes[] = 'function chronoforms_fields_events(){';
			foreach($form->form['Form']['extras']['fields'] as $k => $field){
				if(!empty($field['id']) AND !empty($field['events'])){
					if($field['type'] == 'dropdown'){
						$change_event = 'change';
					}else{
						$change_event = 'click';
					}
					$_f = '$("[name=\''.$field['name'].'\']").on("'.$change_event.'", function(){';
					$_l = '});';
					$_m = array();
					foreach($field['events'] as $k => $event_data){
						if(/*strlen($event_data['state']) AND */strlen($event_data['action']) AND strlen($event_data['target'])){
							$_m[] = $this->create_event($field, $event_data, $form);
						}
					}
					if(!empty($_m)){
						$events_codes[] = $_f."\n".implode("\n", $_m)."\n".$_l;
						$pageload_events_codes[] = implode("\n", $_m);
					}
				}
				if(!empty($field['inputs'])){
					foreach($field['inputs'] as $fn => $field_input){
						if(!empty($field_input['id']) AND !empty($field_input['events'])){
							if($field_input['type'] == 'dropdown'){
								$change_event = 'change';
							}else{
								$change_event = 'click';
							}
							$_f = '$("[name=\''.$field_input['name'].'\']").on("'.$change_event.'", function(){';
							$_l = '});';
							$_m = array();
							foreach($field_input['events'] as $k => $event_data){
								if(/*strlen($event_data['state']) AND */strlen($event_data['action']) AND strlen($event_data['target'])){
									$_m[] = $this->create_event($field_input, $event_data, $form);
								}
							}
							if(!empty($_m)){
								$events_codes[] = $_f."\n".implode("\n", $_m)."\n".$_l;
								$pageload_events_codes[] = implode("\n", $_m);
							}
						}
					}
				}
			}
			//check new fields events
			if(!empty($form->form['Form']['extras']['jsevents'])){
				$jsevents_codes = array();
				foreach($form->form['Form']['extras']['jsevents'] as $k => $jsevent_info){
					if(empty($jsevent_info['source'])){
						continue;
					}
					//$source = $this->get_field_selector($jsevent_info['source'], $form);
					$target = !empty($jsevent_info['target']) ? $this->get_field_selector($jsevent_info['target'], $form) : '';
					$event_trigger = $this->get_event_trigger($jsevent_info, $form);
					$type = $jsevent_info['type'];
					$jsevent_class = '\GCore\Admin\Extensions\Chronoforms\Events\\'.\GCore\Libs\Str::camilize($type).'\\'.\GCore\Libs\Str::camilize($type);
					$jsevents_codes[] = str_replace('__FUNCTION__', $jsevent_class::output($target, $jsevent_info, $form), $event_trigger);
				}
				$events_codes = array_merge($events_codes, $jsevents_codes);
			}
			
			$events_codes[] = '}';
			$events_codes[] = 'chronoforms_fields_events();';
			$events_codes[] = 'function chronoforms_pageload_fields_events(){';
			$events_codes[] = implode("\n", $pageload_events_codes);
			$events_codes[] = '}';
			$events_codes[] = 'chronoforms_pageload_fields_events();';
			
			//$form->execute('js', array('content' => implode("\n", $events_codes)));
		}

		ob_start();
		eval('?>'.$form->form['Form']['content']);
		$output = ob_get_clean();
		$form_content = $output;
		//select the page to display
		$form_pages = explode('<!--_CHRONOFORMS_PAGE_BREAK_-->', $output);
		$active_page_index = (int)$config->get('page', 1) - 1;
		$output = $form_pages[$active_page_index];
		//get current url
		$current_url = \GCore\Libs\Url::current();
		if((bool)$config->get('relative_url', 1) === false){
			$current_url = r_('index.php?ext=chronoforms');
		}
		//generate <form tag
		$form_tag = '<form';
		$form_action = (strlen($config->get('action_url', '')) > 0) ? $config->get('action_url', '') : \GCore\Libs\Url::buildQuery($current_url, array('chronoform' => $form->form['Form']['title'], 'event' => $config->get('submit_event', 'submit')));

		$form_tag .= ' action="'.r_($form_action, (bool)$config->get('xhtml_url', 0)).'"';
		//get method
		$form_method = $config->get('form_method', 'post');
		if($config->get('form_method', 'post') == 'file'){
			$form_tag .= ' enctype="multipart/form-data"';
			$form_method = 'post';
		}
		$form_tag .= ' method="'.$form_method.'"';
		$form_tag .= ' name="'.$form->form['Form']['title'].'"';
		//$form_id = 'chronoform-'.$form->form['Form']['title'];
		$form_tag .= ' id="'.$form_id.'"';
		$form_tag .= ' class="'.$config->get('form_class', 'chronoform').(($theme == 'bootstrap3') ? ' form-horizontal' : '').'"';
		if($config->get('form_tag_attach', '')){
			$form_tag .= ' '.trim($config->get('form_tag_attach', ''));
		}

		$form_tag .= '>';

		if(empty($theme)){
			$doc->_('forms');
		}
		/*
		if($theme == 'bootstrap3'){
			$doc->_('jquery');
			$doc->_('bootstrap');
			//echo '<div class="gcore chronoform-container">';
		}
		*/
		$js_scripts = array();
		
		if(strpos($output, 'data-wysiwyg="1"') !== false){
			$doc->_('jquery');
			$doc->_('editor');
			$js_scripts[] = '$(\'*[data-wysiwyg="1"]\').each(function(){ tinymce.init({"selector":"#"+$(this).attr("id")}); });';
		}
		if(strpos($form_content, 'validate[') !== false){
			$doc->_('jquery');
			$doc->_('gtooltip');
			$doc->_('gvalidation', array('lang' => $form->params->get('js_validation_language', 'en')));
			$js_scripts[] = '$("#'.$form_id.'").gvalidate();';
			$js_scripts[] = '
				$("#'.$form_id.'").find(":input").on("invalid.gvalidation", function(){
					var field = $(this);
					if(field.is(":hidden")){
						if(field.closest(".tab-pane").length > 0){
							var tab_id = field.closest(".tab-pane").attr("id");
							$(\'a[href="#\'+tab_id+\'"]\').closest(".nav").gtabs("get").show($(\'a[href="#\'+tab_id+\'"]\'));
						}
						if(field.closest(".panel-collapse").length > 0){
							var slider_id = field.closest(".panel-collapse").attr("id");
							$(\'a[href="#\'+slider_id+\'"]\').closest(".panel-group").gsliders("get").show($(\'a[href="#\'+slider_id+\'"]\'));
						}
					}
					if(field.data("wysiwyg") == "1"){
						field.data("gvalidation-target", field.parent());
					}
				});
				$("#'.$form_id.'").on("success.gvalidation", function(e){
					if($("#'.$form_id.'").data("gvalidate_success")){
						var gvalidate_success = $("#'.$form_id.'").data("gvalidate_success");
						if(gvalidate_success in window){
							window[gvalidate_success](e, $("#'.$form_id.'"));
						}
					}
				});
				$("#'.$form_id.'").on("fail.gvalidation", function(e){
					if($("#'.$form_id.'").data("gvalidate_fail")){
						var gvalidate_fail = $("#'.$form_id.'").data("gvalidate_fail");
						if(gvalidate_fail in window){
							window[gvalidate_fail](e, $("#'.$form_id.'"));
						}
					}
				});
			';
			if($config->get('required_labels_identify', 1)){
				if(strpos($form->params->get('theme', 'bootstrap3'), 'bootstrap3') !== false){
					$required_icon = '<i class=\'fa fa-asterisk\' style=\'color:#ff0000; font-size:9px; vertical-align:top;\'></i>';
				}else{
					$required_icon = '<span style=\'color:#ff0000; font-size:12px; vertical-align:top;\'>*</span>';
				}
				$js_scripts[] = '
					function chronoforms_validation_signs(formObj){
						formObj.find(":input[class*=validate]").each(function(){
							if($(this).attr("class").indexOf("required") >= 0 || $(this).attr("class").indexOf("group") >= 0){
								var required_parent = [];
								if($(this).closest(".gcore-subinput-container").length > 0){
									var required_parent = $(this).closest(".gcore-subinput-container");
								}else if($(this).closest(".gcore-form-row, .form-group").length > 0){
									var required_parent = $(this).closest(".gcore-form-row, .form-group");
								}
								if(required_parent.length > 0){
									var required_label = required_parent.find("label");
									if(required_label.length > 0 && !required_label.first().hasClass("required_label")){
										required_label.first().addClass("required_label");
										required_label.first().html(required_label.first().html() + " '.$required_icon.'");
									}
								}
							}
						});
					}
					chronoforms_validation_signs($("#chronoform-'.$form->form['Form']['title'].'"));
				';
			}
		}
		if(strpos($form_content, 'data-tooltip') !== false){
			$doc->_('jquery');
			$doc->_('gtooltip');
			if(strpos($form->params->get('theme', 'bootstrap3'), 'bootstrap3') !== false){
				$tip_icon = '<i class=\'fa fa-exclamation-circle input-tooltip\' style=\'color:#2693FF; padding-left:5px;\'></i>';
			}else{
				$tip_icon = '<span style=\'color:#ff0000; font-size:12px; vertical-align:top;\'>!</span>';
			}
			$js_scripts[] = '
				function chronoforms_data_tooltip(formObj){
					formObj.find(":input").each(function(){
						if($(this).data("tooltip") && $(this).closest(".gcore-input, .gcore-input-wide").length > 0){
							var tipped_parent = [];
							if($(this).closest(".gcore-subinput-container").length > 0){
								var tipped_parent = $(this).closest(".gcore-subinput-container");
							}else if($(this).closest(".gcore-form-row, .form-group").length > 0){
								var tipped_parent = $(this).closest(".gcore-form-row, .form-group");
							}
							if(tipped_parent.length > 0){
								var tipped_label = tipped_parent.find("label");
								if(tipped_label.length > 0 && !tipped_label.first().hasClass("tipped_label")){
									tipped_label.first().addClass("tipped_label");
									var $tip = $("'.$tip_icon.'");
									$tip.data("content", $(this).data("tooltip"));
									tipped_label.first().append($tip);
								}
							}
						}
					});
					formObj.find(".input-tooltip").gtooltip();
				}
				chronoforms_data_tooltip($("#chronoform-'.$form->form['Form']['title'].'"));
			';
		}
		if(strpos($form_content, 'data-load-state') !== false){
			$doc->_('jquery');
			$js_scripts[] = '
				function chronoforms_data_loadstate(formObj){
					formObj.find(\':input[data-load-state="disabled"]\').prop("disabled", true);
					formObj.find(\'*[data-load-state="hidden"]\').css("display", "none");
					formObj.find(\':input[data-load-state="hidden_parent"]\').each(function(){
						if($(this).closest(".gcore-subinput-container").length > 0){
							$(this).closest(".gcore-subinput-container").css("display", "none");
						}else if($(this).closest(".gcore-form-row").length > 0){
							$(this).closest(".gcore-form-row").css("display", "none");
						}
					});
				}
				chronoforms_data_loadstate($("#chronoform-'.$form->form['Form']['title'].'"));
			';
		}
		if(strpos($output, 'data-inputmask=') !== false){
			$doc->_('jquery');
			$doc->_('jquery.inputmask');
			$js_scripts[] = '$(":input").inputmask();';
		}
		if(strpos($output, 'data-gdatetimepicker') !== false OR strpos($output, 'data-fieldtype="gdatetimepicker"') !== false){
			$doc->_('jquery');
			$doc->_('gdatetimepicker');
			$js_scripts[] = '
			$(\'*[data-gdatetimepicker-format]\').each(function(){
				$(this).data("format", $(this).data("gdatetimepicker-format"));
			});
			';//for old data attributes
			$js_scripts[] = '$(\'*[data-gdatetimepicker="1"]\').gdatetimepicker();';//for old data attributes
			$js_scripts[] = '$(\'*[data-fieldtype="gdatetimepicker"]\').gdatetimepicker();';
			$js_scripts[] = '
			$(":input").on("select_date.gdatetimepicker", function(){
				if($(this).data("on_date_selected")){
					var on_date_selected = $(this).data("on_date_selected");
					if(on_date_selected in window){
						window[on_date_selected]($(this));
					}
				}
			});
			';
		}
		if(strpos($output, 'multiplier-container') !== false){
			$doc->_('jquery');
			$js_scripts[] = '
				$(".multiplier-container").each(function(){
					if(typeof($(this).data("hide_first")) != "undefined"){
						$(this).find(".multiplier-contents").first().hide();
					}
					if(typeof($(this).data("disable_first")) != "undefined"){
						$(this).find(".multiplier-contents").first().find(":input").prop("disabled", true);
					}
					if($(this).find(".multiplier-contents").length > 1){
						var counter = $(this).find(".multiplier-contents").length;
						$(this).data("count", counter);
					}
				});
				$(".multiplier-container").find(".multiplier-add-button").on("click", function(){
					var multiplier_container = $(this).closest(".multiplier-container");
					
					var multiplier_clone = multiplier_container.find(".multiplier-contents").first().clone();
					multiplier_clone.find(".multiplier-remove-button").first().css("display", "");
					multiplier_clone.show();
					multiplier_clone.find(":input").prop("disabled", false);
					
					if(typeof(multiplier_container.data("replacer")) != "undefined"){
						var counter = parseInt(multiplier_container.data("count"));
						var multiplier_clone = multiplier_clone.wrap("<p>").parent().html().replace(new RegExp(multiplier_container.data("replacer"), "g"), counter);
						multiplier_container.data("count", counter + 1);
					}
					multiplier_container.find(".multiplier-contents").last().after(multiplier_clone);
				});
				$(document).on("click", ".multiplier-remove-button", function(){
					$(this).closest(".multiplier-contents").remove();
				});
			';
		}
		if((bool)$config->get('ajax_submit', 0) === true){
			$doc->_('jquery');
			$doc->_('gtooltip');
			$doc->_('gvalidation', array('lang' => $form->params->get('js_validation_language', 'en')));
			
			$ajax_url = \GCore\Libs\Url::buildQuery($form_action, array('tvout' => 'ajax'));
			$js_scripts[] = '
					function chrono_ajax_submit(){
						$(document).on("click", "#'.$form_id.' :input[type=submit]", function(event){
							$("#'.$form_id.'").append("<input type=\'hidden\' name=\'"+$(this).attr("name")+"\' value=\'"+$(this).val()+"\' />");
						});
						
						var files;
						$("input[type=file]").on("change", function(event){
							files = event.target.files;
						});
						
						$(document).on("submit", "#'.$form_id.'", function(event){
							var overlay = $("<div/>").css({
								"position": "fixed",
								"top": "0",
								"left": "0",
								"width": "100%",
								"height": "100%",
								"background-color": "#000",
								"filter": "alpha(opacity=50)",
								"-moz-opacity": "0.5",
								"-khtml-opacity": "0.5",
								"opacity": "0.5",
								"z-index": "10000",
								"background-image":"url(\"'.\GCore\Helpers\Assets::image('loading-small.gif').'\")",
								"background-position":"center center",
								"background-repeat":"no-repeat",
							});
							if(!$("#'.$form_id.'").hasClass("form-overlayed")){
								$("#'.$form_id.'").append(overlay);
								$("#'.$form_id.'").addClass("form-overlayed");
							}
							var form_action = $("#'.$form_id.'").prop("action");
							var sep = (form_action.indexOf("?") > -1) ? "&" : "?";
							var ajax_url = form_action + sep + "tvout=ajax";
							
							//data processing
							$.ajax({
								"type" : "POST",
								"url" : ajax_url,
								"data" : $("#'.$form_id.'").serialize(),
								"success" : function(res){
									$("#'.$form_id.'").replaceWith(res);
									$("#'.$form_id.'").gvalidate();
									chronoforms_fields_events();
									chronoforms_validation_signs($("#'.$form_id.'"));
									chronoforms_data_tooltip($("#'.$form_id.'"));
									chronoforms_data_loadstate($("#'.$form_id.'"));
									if(typeof chronoforms_pageload_fields_events == "function"){
										chronoforms_pageload_fields_events();
									}
									//chrono_ajax_submit();//this line duplicates submissions, should be removed
								},
							});
							return false;
						});
					}
					chrono_ajax_submit();
				';
		}
		$js_scripts[] = implode("\n", $events_codes);
		
		if(!empty($js_scripts)){
			$doc->addJsCode('jQuery(document).ready(function($){
				'.implode("\n", $js_scripts).'
			});');	
		}
		
		if((bool)$config->get('add_form_tags', 1) === true){
			echo $form_tag;
		}
		//if ajax then display system messages inside the form
		if((bool)$config->get('ajax_submit', 0) === true){
			$doc = \GCore\Libs\Document::getInstance();
			$doc->addCssFile('system_messages');
			$session = \GCore\Libs\Base::getSession();
			$types = $session->getFlash();
			echo \GCore\Helpers\Message::render($types);
		}
		//add fields values
		$output = \GCore\Helpers\DataLoader::load($output, $form->data);
		$output = \GCore\Libs\Str::replacer($output, $form->data, array('repeater' => 'repeater'));
		//show output
		echo $output;
		if((bool)$config->get('add_form_tags', 1) === true){
			echo '</form>';
		}
	}

	function create_event($field, $event_data, $form){
		$return = '';
		$form_id = 'chronoform-'.$form->form['Form']['title'];
		if(empty($event_data['operator'])){
			$event_data['operator'] = '=';
		}
		if($event_data['state'] == 'check'){
			$return .= 'if($("input:checkbox[name=\''.$field['name'].'\']").prop("checked"))';
		}else if($event_data['state'] == 'uncheck'){
			$return .= 'if(!$("input:checkbox[name=\''.$field['name'].'\']").prop("checked"))';
		}else{
			if(in_array($field['type'], array('checkbox_group'))){
				$operator = ($event_data['operator'] == '=') ? '=' : $event_data['operator'];
				$return .= 'if($("[name=\''.$field['name'].'\'][value'.$operator.'\''.$event_data['state'].'\']").prop("checked"))';
			}else if(in_array($field['type'], array('radio'))){
				$operator = ($event_data['operator'] == '=') ? '==' : $event_data['operator'];
				$return .= 'if($("[name=\''.$field['name'].'\']:checked").val() '.$operator.' "'.$event_data['state'].'")';
			}else{
				$operator = ($event_data['operator'] == '=') ? '==' : $event_data['operator'];
				$return .= 'if($("[name=\''.$field['name'].'\']").val() '.$operator.' "'.$event_data['state'].'")';
			}
		}
		$return .= '{'."\n";
		$target_field = '$("#'.$event_data['target'].'")';
		$target = '$("#fin-'.$event_data['target'].', #'.$event_data['target'].'")';
		if($event_data['action'] == 'enable'){
			$return .= $target_field.'.prop("disabled", false);';
		}
		if($event_data['action'] == 'disable'){
			$return .= $target_field.'.prop("disabled", true);';
		}
		if($event_data['action'] == 'show'){
			$return .= $target.'.css("display", "");';
		}
		if($event_data['action'] == 'show_parent'){
			$return .= 'if('.$target.'.closest(".gcore-subinput-container").length > 0){
				'.$target.'.closest(".gcore-subinput-container").css("display", "");
			}else if('.$target.'.closest(".gcore-form-row").length > 0){
				'.$target.'.closest(".gcore-form-row").css("display", "");
			}';
		}
		if($event_data['action'] == 'hide'){
			$return .= $target.'.css("display", "none");';
		}
		if($event_data['action'] == 'hide_parent'){
			$return .= 'if('.$target.'.closest(".gcore-subinput-container").length > 0){
				'.$target.'.closest(".gcore-subinput-container").css("display", "none");
			}else if('.$target.'.closest(".gcore-form-row").length > 0){
				'.$target.'.closest(".gcore-form-row").css("display", "none");
			}';
		}
		if($event_data['action'] == 'set_options'){
			$return .= $target_field.'.find("option").remove();';
			$options = array();
			if(!empty($event_data['options'])){
				$lines = explode("\n", $event_data['options']);
				foreach($lines as $line){
					$opts = explode("=", $line);
					$options[$opts[0]] = $opts[1];
					$return .= $target_field.'.append(\'<option value="'.$opts[0].'">'.trim($opts[1]).'</option>\');'."\n";
				}
			}
		}
		if($event_data['action'] == 'set_dynamic_options'){
			$return .= $target_field.'.find("option").remove();';
			$options = array();
			if(!empty($event_data['options'])){
				$ajax_event = $event_data['options'];
				$return .= '
				$.ajax({
					"type" : "GET",
					"url" : "'.r_('index.php?ext=chronoforms&chronoform='.$form->form['Form']['title'].'&event='.$ajax_event.'&tvout=ajax').'",
					//"data" : {"'.$field['name'].'":$("#'.$field['id'].'").val()},
					"data" : $("#'.$form_id.'").serialize(),
					"success" : function(res){
						$.each($.parseJSON(res), function(id, val){
							'.$target_field.'.append(\'<option value="\'+id+\'">\'+val+\'</option>\');
						});
					},
				});';
				
			}
		}
		if($event_data['action'] == 'set_dynamic_html'){
			if(!empty($event_data['options'])){
				$ajax_event = $event_data['options'];
				$return .= '
				'.$target_field.'.html("<img src=\''.\GCore\Helpers\Assets::image('loading-small.gif').'\' />");
				$.ajax({
					"type" : "GET",
					"url" : "'.r_('index.php?ext=chronoforms&chronoform='.$form->form['Form']['title'].'&event='.$ajax_event.'&tvout=ajax').'",
					"data" : $("#'.$form_id.'").serialize(),
					"success" : function(res){
						'.$target_field.'.html(res);
					},
				});';
				
			}
		}
		if($event_data['action'] == 'function'){
			$return .= $event_data['target'].';';
		}
		$return .= "\n".'}';
		return $return;
	}
	
	function get_field_selector($field_id, $form){
		if(!empty($field_id)){
			$field = $this->get_field($field_id, $form);
			$selector = ':input[name="'.$field['name'].'"]';
			if(in_array($field['type'], array('container'))){
				$selector = '#'.$field['id'];
			}
			return $selector;
		}
		return false;
	}
	
	function get_field($field_id, $form){
		if(!empty($field_id)){
			if(strpos($field_id, '-') !== false){
				$ids = explode('-', $field_id);
				return $form->form['Form']['extras']['fields'][$ids[0]]['inputs'][$ids[1]];
			}else{
				return $form->form['Form']['extras']['fields'][$field_id];
			}
		}
		return false;
	}
	
	function get_event_trigger($jsevent, $form){
		$code = '';
		$event = $jsevent['event'];
		$source = $this->get_field_selector($jsevent['source'], $form);
		//$field = $form->form['Form']['extras']['fields'][$jsevent['source']];
		$field = $this->get_field($jsevent['source'], $form);
		switch($event){
			case 'check':
				$code = "
					$('".$source."').on('click', function(){
						if($(this).prop('checked')){
							__FUNCTION__
						}
					});";
					break;
			case 'uncheck':
				$code = "
					$('".$source."').on('click', function(){
						if($(this).prop('checked') == false){
							__FUNCTION__
						}
					});";
					break;
			case 'change_to':
				$checked = '';
				if(in_array($field['type'], array('radio', 'checkbox_group'))){
					$checked = "$(this).prop('checked') && ";
				}
				$code = "
					$('".$source."').on('change', function(){
						if(".$checked."$(this).val() == '".$jsevent['value']."'){
							__FUNCTION__
						}
					});";
					break;
			case 'change_not':
				$checked = '';
				if(in_array($field['type'], array('radio', 'checkbox_group'))){
					$checked = "$(this).prop('checked') && ";
				}
				$code = "
					$('".$source."').on('change', function(){
						if(".$checked."$(this).val() != '".$jsevent['value']."'){
							__FUNCTION__
						}
					});";
					break;
			case 'click':
			case 'change':
			case 'keydown':
			case 'keyup':
				$code = "
					$('".$source."').on('".$event."', function(){
						__FUNCTION__
					});";
					break;
		}
		
		return $code;
	}

	public static function config(){
		echo \GCore\Helpers\Html::formStart('action_config html_action_config', 'html_action_config__XNX_');
		echo \GCore\Helpers\Html::formSecStart();

		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][page]', array('type' => 'text', 'label' => l_('CF_PAGE'), 'value' => 1, 'sublabel' => l_('CF_PAGE_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][submit_event]', array('type' => 'text', 'label' => l_('CF_SUBMIT_EVENT'), 'value' => 'submit', 'sublabel' => l_('CF_SUBMIT_EVENT_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][form_method]', array('type' => 'dropdown', 'label' => l_('CF_FORM_METHOD'), 'options' => array('file' => 'File', 'post' => 'Post', 'get' => 'Get'), 'sublabel' => l_('CF_FORM_METHOD_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][action_url]', array('type' => 'text', 'label' => l_('CF_ACTION_URL'), 'class' => 'XL', 'sublabel' => l_('CF_ACTION_URL_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][form_class]', array('type' => 'text', 'label' => l_('CF_FORM_CLASS'), 'value' => 'chronoform', 'sublabel' => l_('CF_FORM_CLASS_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][form_tag_attach]', array('type' => 'text', 'label' => l_('CF_FORM_TAG_ATTACHMENT'), 'class' => 'XL', 'rows' => 1, 'sublabel' => l_('CF_FORM_TAG_ATTACHMENT_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][required_labels_identify]', array('type' => 'dropdown', 'label' => l_('CF_REQUIRED_LABELS_IDENTIFY'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'values' => 1, 'sublabel' => l_('CF_REQUIRED_LABELS_IDENTIFY_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][relative_url]', array('type' => 'dropdown', 'label' => l_('CF_RELATIVE_URL'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'values' => 1, 'sublabel' => l_('CF_RELATIVE_URL_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][ajax_submit]', array('type' => 'dropdown', 'label' => l_('CF_AJAX_SUBMIT'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'values' => 0, 'sublabel' => l_('CF_AJAX_SUBMIT_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][add_form_tags]', array('type' => 'dropdown', 'label' => l_('CF_ADD_FORM_TAGS'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'values' => 1, 'sublabel' => l_('CF_ADD_FORM_TAGS_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][xhtml_url]', array('type' => 'dropdown', 'label' => l_('CF_HTML_XHTML_URL'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'values' => 0, 'sublabel' => l_('CF_HTML_XHTML_URL_DESC')));

		echo \GCore\Helpers\Html::formSecEnd();
		echo \GCore\Helpers\Html::formEnd();
	}
	
	public static function config_check($data = array()){
		$diags = array();
		$diags[l_('CF_DIAG_PAGE')] = !empty($data['page']) ? $data['page'] : 1;
		$diags[l_('CF_DIAG_SUBMIT_EVENT')] = !empty($data['submit_event']) ? $data['submit_event'] : 'submit';
		$diags[l_('CF_DIAG_ACTION_URL')] = empty($data['action_url']);
		$diags[l_('CF_DIAG_AJAX')] = !empty($data['ajax_submit']) ? $data['ajax_submit'] : 0;
		return $diags;
	}
}