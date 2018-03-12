<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Actions\SessionToData;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
Class SessionToData extends \GCore\Admin\Extensions\Chronoforms\Action{
	static $title = 'Session To Data';
	static $group = array('session' => 'Session');

	var $defaults = array(
		'namespace' => '',
		'key' => '',
		'clear' => 0,
		'overwrite_old_data' => 0,
	);

	function execute(&$form, $action_id){
		$config =  $form->actions_config[$action_id];
		$config = new \GCore\Libs\Parameter($config);

		$session = \GCore\Libs\Base::getSession();
		$session_ns = $config->get('namespace', '');
		$session_key = $config->get('key', '');
		if(empty($session_key)){
			$session_key = $form->form['Form']['title'];
		}
		if(empty($session_ns)){
			$session_ns = 'default';
		}
		if(!$config->get('overwrite_old_data', 0)){
			$form->data = array_merge($form->data, $session->get('_chronoform_data_'.$session_key, array()));
		}else{
			$form->data = array_merge($session->get('_chronoform_data_'.$session_key, array()), $form->data);
		}
		//clear the session if the clear option is set to yes
		if((bool)$config->get('clear', 0) === true){
			$session->clear('_chronoform_data_'.$session_key);
		}
	}

	public static function config(){
		echo \GCore\Helpers\Html::formStart('action_config session_to_data_action_config', 'session_to_data_action_config__XNX_');
		echo \GCore\Helpers\Html::formSecStart();
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][clear]', array('type' => 'dropdown', 'label' => l_('CF_SESSION_CLEAR'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'sublabel' => l_('CF_SESSION_CLEAR_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][key]', array('type' => 'text', 'label' => l_('CF_SESSION_KEY'), 'sublabel' => l_('CF_SESSION_KEY_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][overwrite_old_data]', array('type' => 'dropdown', 'label' => l_('CF_SESSION_DATA_OVERWRITE_OLD_DATA'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'sublabel' => l_('CF_SESSION_DATA_OVERWRITE_OLD_DATA_DESC')));
		echo \GCore\Helpers\Html::formSecEnd();
		echo \GCore\Helpers\Html::formEnd();
	}
}