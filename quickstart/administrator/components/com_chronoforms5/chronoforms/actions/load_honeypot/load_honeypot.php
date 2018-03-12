<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Actions\LoadHoneypot;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
Class LoadHoneypot extends \GCore\Admin\Extensions\Chronoforms\Action{
	static $title = 'Load Honeypot';
	static $group = array('anti_spam' => 'Anti Spam');

	function execute(&$form, $action_id){
		$config =  $form->actions_config[$action_id];
		$config = new \GCore\Libs\Parameter($config);
		$session = \GCore\Libs\Base::getSession();
		
		//extract questions
		$field_name = \GCore\Libs\Str::rand();
		
		$session_key = $config->get('session_key', '');
		if(empty($session_key)){
			$session_key = $form->form['Form']['title'];
		}
		
		$session->set('chrono_honeypot_'.$session_key, array('name' => $field_name, 'time' => time()));
		
		$field_code = \GCore\Helpers\Html::input($field_name, array('type' => 'hidden', 'value' => 1));
		if($config->get('method', 'static') == 'static'){
			$form->form['Form']['content'] = $form->form['Form']['content'].$field_code;
		}else{
			$doc = \GCore\Libs\Document::getInstance();
			$doc->addJsCode('
			jQuery(document).ready(function($){
				$("#chronoform-'.$form->form['Form']['title'].'").append(\''.$field_code.'\');
			});
			');
		}
	}

	public static function config(){
		echo \GCore\Helpers\Html::formStart('action_config load_honeypot_action_config', 'load_honeypot_action_config__XNX_');
		echo \GCore\Helpers\Html::formSecStart();
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][method]', array('type' => 'dropdown', 'label' => l_('CF_HONEYPOT_METHOD'), 'options' => array('static' => l_('CF_STATIC'), 'dynamic' => l_('CF_DYNAMIC')), 'sublabel' => l_('CF_HONEYPOT_METHOD_DESC')));
		echo \GCore\Helpers\Html::formSecEnd();
		echo \GCore\Helpers\Html::formEnd();
	}
}