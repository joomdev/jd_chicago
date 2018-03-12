<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Actions\LoadNocaptcha;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
Class LoadNocaptcha extends \GCore\Admin\Extensions\Chronoforms\Action{
	static $title = 'Load Google NoCaptcha';
	static $group = array('anti_spam' => 'Anti Spam');
	var $defaults = array(
		'site_key' => '',
		//'ssl_server' => '0',
		'theme' => 'red',
		'lang' => 'en',
		//'api_server' => 'http://www.google.com/recaptcha/api',
		//'api_secure_server' => 'https://www.google.com/recaptcha/api'
	);

	function execute(&$form, $action_id){
		$config =  $form->actions_config[$action_id];
		$config = new \GCore\Libs\Parameter($config);
		
		$recaptcha_load = '<div class="g-recaptcha" data-sitekey="'.$config->get('site_key').'"></div>';
		
		$doc = \GCore\Libs\Document::getInstance();
        $doc->addJsFile('https://www.google.com/recaptcha/api.js');
		//replace the string
		$form->form['Form']['content'] = str_replace('{ReCaptcha}', $recaptcha_load, $form->form['Form']['content']);
	}

	public static function config(){
		echo \GCore\Helpers\Html::formStart('action_config load_nocaptcha_action_config', 'load_nocaptcha_action_config__XNX_');
		echo \GCore\Helpers\Html::formSecStart();
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][site_key]', array('type' => 'text', 'label' => l_('CF_NOCAPTCHA_SITE_KEY'), 'class' => 'XL', 'sublabel' => l_('CF_NOCAPTCHA_SITE_KEY_DESC')));
		/*echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][theme]', array('type' => 'dropdown', 'label' => l_('CF_RECAPTCHA_THEME'), 'options' => array(
					'clean' => 'Clean', 
					'red' => 'Red',
					'white' => 'White',
					'blackglass' => 'Blackglass',
					'custom' => 'Custom'
				), 'sublabel' => l_('CF_RECAPTCHA_THEME_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][lang]', array('type' => 'dropdown', 'label' => l_('CF_RECAPTCHA_LANG'), 'options' => array(
					'en' => 'English', 
					'nt' => 'Dutch',
					'fr' => 'French',
					'de' => 'German',
					'pt' => 'Portuguese',
					'ru' => 'Russian',
					'es' => 'Spanish',
					'tr' => 'Turkish'
				), 'sublabel' => l_('CF_RECAPTCHA_LANG_DESC')));*/
		echo \GCore\Helpers\Html::formSecEnd();
		echo \GCore\Helpers\Html::formEnd();
	}
}