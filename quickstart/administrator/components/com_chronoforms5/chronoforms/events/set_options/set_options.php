<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Events\SetOptions;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
class SetOptions extends \GCore\Admin\Extensions\Chronoforms\Events\Event{
	static $title = 'Set Options';
	static $cat_id = 'basic';
	static $cat_title = 'Basic';
	
	public static function config($data = array(), $k = '_XNX_'){
		echo \GCore\Helpers\Html::formStart('jsevent_config', 'set_options_config_'.$k);
		echo \GCore\Helpers\Html::formSecStart();
		
		echo \GCore\Helpers\Html::formLine('-', array('type' => 'multi', 'inputs' => array_merge(self::_fields($data, $k), array(
			array('name' => 'Form[extras][jsevents]['.$k.'][target]', 'type' => 'dropdown', 'label' => array('position' => 'top', 'text' => l_('CF_EVENT_SET_OPTIONS')), 'sublabel' => l_('CF_EVENT_ELEMENT'), 'options' => self::$fields),
			array('name' => 'Form[extras][jsevents]['.$k.'][options]', 'type' => 'textarea', 'rows' => 5, 'cols' => 60, 'label' => array('position' => 'top', 'text' => l_('CF_EVENT_OPTIONS')), 'sublabel' => l_('CF_EVENT_OPTIONS_DESC')),
		))));
		
		echo \GCore\Helpers\Html::input('Form[extras][jsevents]['.$k.'][type]', array('type' => 'hidden', 'value' => 'set_options'));
		echo \GCore\Helpers\Html::formSecEnd();
		echo \GCore\Helpers\Html::formEnd();
	}
	
	public static function output($target, $event){
		$return = "$('".$target."').find('option').remove();";
		$options = \GCore\Libs\Str::list_to_array($event['options']);
		foreach($options as $v => $t){
			$return .= "$('".$target."').append('<option value=\"".$v."\">".$t."</option>');"."\n";
		}
		return $return;
	}
}
?>