<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Actions\LoadForm;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
Class LoadForm extends \GCore\Admin\Extensions\Chronoforms\Action{
	static $title = 'Load Form';
	static $group = array('utilities' => 'Utilities');
	
	var $defaults = array(
		'form_event' => 'load',
	);

	function execute(&$form, $action_id){
		$config = !empty($form->actions_config[$action_id]) ? $form->actions_config[$action_id] : array();
		$config = new \GCore\Libs\Parameter($config);
		
		if($config->get('form_name', '') AND $config->get('form_name', '') != $form->form['Form']['title']){
			$form2 = \GCore\Extensions\Chronoforms\Libs\Form::getInstance($config->get('form_name', ''));
			$form2->process(array($config->get('form_event', 'load')));
		}
	}

	public static function config(){
		echo \GCore\Helpers\Html::formStart('action_config load_form_action_config', 'load_form_action_config__XNX_');
		echo \GCore\Helpers\Html::formSecStart();

		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][form_name]', array('type' => 'text', 'label' => l_('CF_LOADFORM_FORM_NAME'), 'class' => 'L', 'sublabel' => l_('CF_LOADFORM_FORM_NAME_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][form_event]', array('type' => 'text', 'label' => l_('CF_LOADFORM_FORM_EVENT'), 'class' => 'M', 'sublabel' => l_('CF_LOADFORM_FORM_EVENT_DESC')));
		
		echo \GCore\Helpers\Html::formSecEnd();
		echo \GCore\Helpers\Html::formEnd();
	}
	
	public static function config_check($data = array()){
		$diags = array();
		$diags[l_('CF_DIAG_FORMNAME_SET')] = !empty($data['form_name']);
		return $diags;
	}
}