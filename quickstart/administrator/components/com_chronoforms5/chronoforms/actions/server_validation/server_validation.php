<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Actions\ServerValidation;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
Class ServerValidation extends \GCore\Admin\Extensions\Chronoforms\Action{
	static $title = 'Server Validation';
	static $group = array('validation' => 'Validation');

	var $events = array('success' => 0, 'fail' => 0);
	
	var $defaults = array(
		'default_error' => 'Error occurred.',
		'display_errors_top' => 1,
		'highlight_fields' => 1,
	);

	function execute(&$form, $action_id){
		$config =  $form->actions_config[$action_id];
		$config = new \GCore\Libs\Parameter($config);
		
		$failed = false;
		$failed_fields = array();
		foreach($config->get('rules') as $rule => $data){
			if(!empty($data)){
				$fields = explode("\n", $data);
				foreach($fields as $field){
					$fch = explode(':', $field);
					if(count($fch)){
						if(!in_array($rule, array('not_empty', 'is_empty')) AND strlen((string)$form->data($fch[0])) == 0){
							continue;
						}
						$valid = \GCore\Libs\Validate::$rule($form->data($fch[0]));
						if(!$valid){
							$failed = true;
							if($config->get('display_errors_top', 1)){
								$form->errors[] = $failed_fields[trim($fch[0])] = isset($fch[1]) ? trim($fch[1]) : $config->get('default_error', 'Error occurred.');
							}else{
								$failed_fields[trim($fch[0])] = isset($fch[1]) ? trim($fch[1]) : $config->get('default_error', 'Error occurred.');
							}
						}
					}
				}
			}
		}
		
		
		if($config->get('highlight_fields', 1) AND !empty($failed_fields)){
			$doc = \GCore\Libs\Document::getInstance();
			$doc->_('jquery');
			$doc->_('gtooltip');
			$doc->_('gvalidation');
			$doc->addCssCode('
				.server_validation_error{
					background-color:red;
					color:#fff;
					display:block;
					padding:3px;
					font-size:12px;
					line-height:13px;
					margin-top:3px;
					border-radius:3px;
				}
			');
			$form_id = 'chronoform-'.$form->form['Form']['title'];
			$sts = array();
			foreach($failed_fields as $failed_field => $error){
				$sts[] = 'jQuery("#'.$form_id.' :input[name^='.$failed_field.']").closest(".gcore-input").append("<span class=\"server_validation_error\">'.$error.'</span>");';
			}
			$doc->addJsCode('
				jQuery(document).ready(function(){
					'.implode("\n", $sts).'
				});
			');
		}
		
		if($failed){
			$this->events['fail'] = 1;
		}else{
			$this->events['success'] = 1;
		}
	}

	public static function config(){
		echo \GCore\Helpers\Html::formStart('action_config server_validation_action_config', 'server_validation_action_config__XNX_');
		echo \GCore\Helpers\Html::formSecStart();
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][action_label]', array('type' => 'text', 'label' => l_('CF_ACTION_LABEL'), 'class' => 'XL', 'sublabel' => l_('CF_ACTION_LABEL_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][default_error]', array('type' => 'text', 'label' => l_('CF_SV_DEFAULT_ERROR'), 'class' => 'XL', 'sublabel' => l_('CF_SV_DEFAULT_ERROR_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][display_errors_top]', array('type' => 'dropdown', 'label' => l_('CF_SV_TOP_ERRORS'), 'values' => 1, 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'sublabel' => l_('CF_SV_TOP_ERRORS_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][highlight_fields]', array('type' => 'dropdown', 'label' => l_('CF_SV_HIGHLIGHT_FIELDS'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'sublabel' => l_('CF_SV_HIGHLIGHT_FIELDS_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][rules][not_empty]', array('type' => 'textarea', 'label' => l_('CF_NOT_EMPTY'), 'rows' => 5, 'cols' => 60, 'sublabel' => l_('CF_NOT_EMPTY_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][rules][is_empty]', array('type' => 'textarea', 'label' => l_('CF_EMPTY'), 'rows' => 5, 'cols' => 60, 'sublabel' => ''));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][rules][no_spaces]', array('type' => 'textarea', 'label' => l_('CF_NO_SPACES'), 'rows' => 5, 'cols' => 60, 'sublabel' => ''));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][rules][alpha]', array('type' => 'textarea', 'label' => l_('CF_ALPHA'), 'rows' => 5, 'cols' => 60, 'sublabel' => ''));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][rules][alphanumeric]', array('type' => 'textarea', 'label' => l_('CF_ALPHA_NUMERIC'), 'rows' => 5, 'cols' => 60, 'sublabel' => ''));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][rules][digit]', array('type' => 'textarea', 'label' => l_('CF_DIGIT'), 'rows' => 5, 'cols' => 60, 'sublabel' => ''));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][rules][nodigit]', array('type' => 'textarea', 'label' => l_('CF_NO_DIGIT'), 'rows' => 5, 'cols' => 60, 'sublabel' => ''));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][rules][number]', array('type' => 'textarea', 'label' => l_('CF_NUMBER'), 'rows' => 5, 'cols' => 60, 'sublabel' => ''));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][rules][email]', array('type' => 'textarea', 'label' => l_('CF_EMAIL'), 'rows' => 5, 'cols' => 60, 'sublabel' => ''));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][rules][phone]', array('type' => 'textarea', 'label' => l_('CF_PHONE'), 'rows' => 5, 'cols' => 60, 'sublabel' => ''));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][rules][phone_inter]', array('type' => 'textarea', 'label' => l_('CF_INT_PHONE'), 'rows' => 5, 'cols' => 60, 'sublabel' => ''));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][rules][url]', array('type' => 'textarea', 'label' => l_('CF_URL'), 'rows' => 5, 'cols' => 60, 'sublabel' => ''));
		echo \GCore\Helpers\Html::formSecEnd();
		echo \GCore\Helpers\Html::formEnd();
	}
}