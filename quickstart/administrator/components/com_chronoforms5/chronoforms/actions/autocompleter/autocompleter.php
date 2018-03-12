<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Actions\Autocompleter;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
Class Autocompleter extends \GCore\Admin\Extensions\Chronoforms\Action{
	static $title = 'Autocompleter';
	static $group = array('utilities' => 'Utilities');
	
	var $defaults = array(
		'no_matches_msg' => 'Error!!',
		'searching_msg' => 'Loading....',
		'ajax_error_msg' => 'Loading failed....',
		'field_name' => 'tag',
	);

	function execute(&$form, $action_id){
		$config = !empty($form->actions_config[$action_id]) ? $form->actions_config[$action_id] : array();
		$config = new \GCore\Libs\Parameter($config);
		$doc = \GCore\Libs\Document::getInstance();

		$ajax_url = $config->get('results_event', '') ? r_(\GCore\C::get('GCORE_ROOT_URL').'index.php?ext=chronoforms&chronoform='.$form->form['Form']['title'].'&event='.$config->get('results_event', '').'&tvout=ajax') : $config->get('results_url', '');

		$doc->_('jquery');
		//$doc->_('autocompleter');
		//$doc->__('autocompleter', $config->get('field_selector', '.auto_complete'), array('path' => $ajax_url, 'length' => $config->get('length', 2), 'multiple' => $config->get('multiple', 0)));
	
		$doc->_('select2');
		$doc->addJsCode('
			jQuery(document).ready(function($){
				$("'.$config->get('field_selector', '').'").select2(
					{
						minimumInputLength: '.$config->get('length', 2).',
						containerCss:{"min-width":"200px"},
						width: "element",
						multiple: '.($config->get('multiple', 0) ? 'true' : 'false').',
						//tags: true,
						tokenSeparators: [","," "],
						ajax:{
							url: "'.$ajax_url.'",
							dataType: "json",
							data: function (term, page){
								return {
									'.$config->get('field_name', 'tag').': term,
								};
							},
							results: function (data, page){
								return {results: data};
							}
						},
						formatNoMatches: "'.$config->get('no_matches_msg', 'Error!!').'",
						formatSearching: "'.$config->get('searching_msg', 'Loading....').'",
						formatAjaxError: "'.$config->get('ajax_error_msg', 'Loading failed....').'",
						'.($config->get('multiple', 0) ? '
						initSelection: function(element, callback){
							var data = [];
							$(element.val().split(",")).each(function (){
								data.push({id: this, text: this});
							});
							callback(data);
						}
						' : '
						initSelection: function(element, callback){
							var data = {"id": $(element).val(), "text": $(element).val()};
							callback(data);
						}
						').'
					}
				);
			});'
		);
		
		
	}

	public static function config(){
		echo \GCore\Helpers\Html::formStart('action_config autocompleter_action_config', 'autocompleter_action_config__XNX_');
		echo \GCore\Helpers\Html::formSecStart();

		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][field_selector]', array('type' => 'text', 'label' => l_('CF_AC_FIELD_SELECTOR'), 'class' => 'L', 'sublabel' => l_('CF_AC_FIELD_SELECTOR_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][field_name]', array('type' => 'text', 'label' => l_('CF_AC_FIELD_NAME'), 'class' => 'M', 'sublabel' => l_('CF_AC_FIELD_NAME_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][results_url]', array('type' => 'text', 'label' => l_('CF_AC_RESULTS_URL'), 'class' => 'XXL', 'sublabel' => l_('CF_AC_RESULTS_URL_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][results_event]', array('type' => 'text', 'label' => l_('CF_AC_RESULTS_EVENT'), 'class' => 'L', 'sublabel' => l_('CF_AC_RESULTS_EVENT_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][length]', array('type' => 'text', 'label' => l_('CF_AC_LENGTH'), 'value' => '2', 'sublabel' => l_('CF_AC_LENGTH_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][multiple]', array('type' => 'dropdown', 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'label' => l_('CF_AC_MULTIPLE'), 'sublabel' => l_('CF_AC_MULTIPLE_DESC')));
		
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][no_matches_msg]', array('type' => 'text', 'label' => l_('CF_AC_NOMATCHES_MSG'), 'class' => 'L', 'sublabel' => l_('CF_AC_NOMATCHES_MSG_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][searching_msg]', array('type' => 'text', 'label' => l_('CF_AC_SEARCHING_MSG'), 'class' => 'L', 'sublabel' => l_('CF_AC_SEARCHING_MSG_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][ajax_error_msg]', array('type' => 'text', 'label' => l_('CF_AC_AJAX_ERROR_MSG'), 'class' => 'L', 'sublabel' => l_('CF_AC_AJAX_ERROR_MSG_DESC')));
		
		echo \GCore\Helpers\Html::formSecEnd();
		echo \GCore\Helpers\Html::formEnd();
	}
	
	public static function config_check($data = array()){
		$diags = array();
		$diags[l_('CF_DIAG_FIELDSELECTOR_SET')] = !empty($data['field_selector']);
		$diags[l_('CF_DIAG_FIELDNAME_SET')] = !empty($data['field_name']);
		$diags[l_('CF_DIAG_RESULTS_SOURCE_SET')] = (!empty($data['results_url']) OR !empty($data['results_event']));
		return $diags;
	}
}