<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Actions\CheckHoneypot;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
Class CheckHoneypot extends \GCore\Admin\Extensions\Chronoforms\Action{
	static $title = 'Check Honeypot';
	//static $setup = array('simple' => array('title' => 'Captcha'));
	static $group = array('anti_spam' => 'Anti Spam');

	var $events = array('success' => 0, 'fail' => 0);

	var $defaults = array(
		'error' => "Honeypot check failed.",
	);

	function execute(&$form, $action_id){
		$config =  $form->actions_config[$action_id];
		$config = new \GCore\Libs\Parameter($config);
		
		$session = \GCore\Libs\Base::getSession();
		$session_key = $config->get('session_key', '');
		if(empty($session_key)){
			$session_key = $form->form['Form']['title'];
		}
		$sessionvar = $session->get('chrono_honeypot_'.$session_key, array());
		$session->clear('chrono_honeypot_'.$session_key);
		
		$field_name = !empty($sessionvar['name']) ? $sessionvar['name'] : '';
		$time = !empty($sessionvar['time']) ? $sessionvar['time'] : time();
		
		//check field exists
		if(!empty($field_name) AND !empty($form->data[$field_name])){
			//check time
			if((int)$config->get('time', 5) + $time > time()){
				$this->events['fail'] = 1;
				$form->errors['chrono_honeypot'] = $config->get('error', "Honeypot check failed.");
				$form->debug[$action_id][self::$title][] = "Time too short";
				return false;
			}
		}else{
			$this->events['fail'] = 1;
			$form->errors['chrono_honeypot'] = $config->get('error', "Honeypot check failed.");
			$form->debug[$action_id][self::$title][] = "Token mismatch";
			return false;
		}
		$this->events['success'] = 1;
		$form->debug[$action_id][self::$title][] = "Honeypot check passed.";
		return true;
	}

	public static function config(){
		echo \GCore\Helpers\Html::formStart('action_config check_honeypot_action_config', 'check_honeypot_action_config__XNX_');
		echo \GCore\Helpers\Html::formSecStart();
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][time]', array('type' => 'text', 'label' => l_('CF_HONEYPOT_TIME'), 'value' => 5, 'sublabel' => l_('CF_HONEYPOT_TIME_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][error]', array('type' => 'text', 'label' => l_('CF_HONEYPOT_ERROR'), 'class' => 'XL', 'sublabel' => l_('CF_HONEYPOT_ERROR_DESC')));
		echo \GCore\Helpers\Html::formSecEnd();
		echo \GCore\Helpers\Html::formEnd();
	}
}