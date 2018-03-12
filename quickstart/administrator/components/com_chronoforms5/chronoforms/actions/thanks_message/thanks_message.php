<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Actions\ThanksMessage;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
Class ThanksMessage extends \GCore\Admin\Extensions\Chronoforms\Action{
	static $title = 'Display Message';
	static $setup = array('simple' => array('title' => 'Thanks Message'));

	function execute(&$form, $action_id){
		$config =  $form->actions_config[$action_id];
		$config = new \GCore\Libs\Parameter($config);
		$message = $config->get('message', '');
		echo \GCore\Libs\Str::replacer($message, $form->data, array('repeater' => 'repeater'));
	}

	public static function config(){
		echo \GCore\Helpers\Html::formStart('action_config thanks_message_action_config', 'thanks_message_action_config__XNX_');
		echo \GCore\Helpers\Html::formSecStart();
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][action_label]', array('type' => 'text', 'label' => l_('CF_ACTION_LABEL'), 'class' => 'XL', 'sublabel' => l_('CF_ACTION_LABEL_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][load_editor]', array('type' => 'button', 'class' => 'btn btn-primary', 'value' => l_('CF_LOAD_EDITOR'), 'onclick' => 'toggleEditor(this, \'thanks_message_content__XNX_\');'));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][message]', array('type' => 'textarea', 'label' => l_('CF_MESSAGE'), 'id' => 'thanks_message_content__XNX_', 'rows' => 20, 'cols' => 70, 'sublabel' => l_('CF_THANKS_MESSAGE_DESC')));
		echo \GCore\Helpers\Html::formSecEnd();
		echo \GCore\Helpers\Html::formEnd();
	}
}