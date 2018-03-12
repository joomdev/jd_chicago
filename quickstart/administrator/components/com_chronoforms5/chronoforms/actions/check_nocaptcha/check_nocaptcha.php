<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Actions\CheckNocaptcha;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
Class CheckNocaptcha extends \GCore\Admin\Extensions\Chronoforms\Action{
	static $title = 'Check Google NoCaptcha';
	//static $setup = array('simple' => array('title' => 'Captcha'));
	static $group = array('anti_spam' => 'Anti Spam');

	var $events = array('success' => 0, 'fail' => 0);

	var $defaults = array(
		'secret_key' => '',
		//'verify_server' => 'www.google.com',
		'error' => "The reCAPTCHA wasn't entered correctly. Please try it again."
	);

	function execute(&$form, $action_id){
		$config =  $form->actions_config[$action_id];
		$config = new \GCore\Libs\Parameter($config);
		
		$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$config->get('secret_key')."&response=".$form->data('g-recaptcha-response'));
		$response = json_decode($response, true);
		if($response["success"] === true){
			$this->events['success'] = 1;
		}else{
			$form->errors['recaptcha'] = $config->get('error', "The reCAPTCHA wasn't entered correctly. Please try it again.");
			$form->debug[$action_id][self::$title][] = $response["error-codes"];
			$this->events['fail'] = 1;
		}
	}
	
	public static function config(){
		echo \GCore\Helpers\Html::formStart('action_config check_nocaptcha_action_config', 'check_nocaptcha_action_config__XNX_');
		echo \GCore\Helpers\Html::formSecStart();
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][error]', array('type' => 'text', 'label' => l_('CF_NOCAPTCHA_ERROR'), 'class' => 'XL', 'sublabel' => l_('CF_NOCAPTCHA_ERROR_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][secret_key]', array('type' => 'text', 'label' => l_('CF_NOCAPTCHA_SECRET_KEY'), 'class' => 'XL', 'sublabel' => l_('CF_NOCAPTCHA_SECRET_KEY_DESC')));
		echo \GCore\Helpers\Html::formSecEnd();
		echo \GCore\Helpers\Html::formEnd();
	}
}