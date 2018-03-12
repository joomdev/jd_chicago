<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Events\DisableValidation;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
class DisableValidation extends \GCore\Admin\Extensions\Chronoforms\Events\Event{
	static $title = 'Disable Validation';
	static $cat_id = 'basic';
	static $cat_title = 'Basic';
	
	public static function config($data = array(), $k = '_XNX_'){
		echo \GCore\Helpers\Html::formStart('jsevent_config', 'enable_config_'.$k);
		echo \GCore\Helpers\Html::formSecStart();
		
		echo \GCore\Helpers\Html::formLine('-', array('type' => 'multi', 'inputs' => array_merge(self::_fields($data, $k), array(
			array('name' => 'Form[extras][jsevents]['.$k.'][target]', 'type' => 'dropdown', 'label' => array('position' => 'top', 'text' => l_('CF_EVENT_DISABLE_VALIDATION')), 'sublabel' => l_('CF_EVENT_ELEMENT'), 'options' => self::$fields),
		))));
		
		echo \GCore\Helpers\Html::input('Form[extras][jsevents]['.$k.'][type]', array('type' => 'hidden', 'value' => 'disable_validation'));
		echo \GCore\Helpers\Html::formSecEnd();
		echo \GCore\Helpers\Html::formEnd();
	}
	
	public static function output($target, $event){
		$return = "";
		//$return = "$('".$target."').addClass('validate[\"required\"]');";
		$return .= "$('".$target."').gvalidate();";
		$return .= "$('".$target."').gvalidate('get').disable();";
		return $return;
	}
}
?>