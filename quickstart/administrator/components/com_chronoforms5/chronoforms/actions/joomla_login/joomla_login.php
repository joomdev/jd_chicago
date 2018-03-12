<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Actions\JoomlaLogin;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
Class JoomlaLogin extends \GCore\Admin\Extensions\Chronoforms\Action{
	static $title = 'Joomla Login';
	//static $setup = array('simple' => array('title' => 'Permissions'));
	static $group = array('joomla' => 'Joomla');
	static $platforms = array('joomla');
	var $events = array('success' => 0, 'fail' => 0);

	var $defaults = array(
		'username' => '',
		'password' => '',
	);

	function execute(&$form, $action_id){
		$config =  $form->actions_config[$action_id];
		$config = new \GCore\Libs\Parameter($config);
		
		$mainframe = \JFactory::getApplication();
		// Get required system objects
		\JRequest::setVar('username', \JRequest::getVar($config->get('username', '')));
		\JRequest::setVar('password', \JRequest::getVar($config->get('password', '')));
		
		$credentials = array();
		$credentials['username'] = \JRequest::getVar('username');
		$credentials['password'] = \JRequest::getVar('password');
		if($mainframe->login($credentials) === true){
			$this->events['success'] = 1;
		}else{
			$this->events['fail'] = 1;
			$form->errors[] = 'Invalid username or password.';
			return false;
		}
	}

	public static function config(){
		echo \GCore\Helpers\Html::formStart('action_config joomla_login_action_config', 'joomla_login_action_config__XNX_');
		echo \GCore\Helpers\Html::formSecStart();
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][username]', array('type' => 'text', 'label' => l_('CF_JOOMLA_REG_USERNAME'), 'class' => 'M', 'sublabel' => l_('CF_JOOMLA_REG_USERNAME_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][password]', array('type' => 'text', 'label' => l_('CF_JOOMLA_REG_PASSWORD'), 'class' => 'M', 'sublabel' => l_('CF_JOOMLA_REG_PASSWORD_DESC')));
		echo \GCore\Helpers\Html::formSecEnd();
		echo \GCore\Helpers\Html::formEnd();
	}
}