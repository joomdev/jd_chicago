<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Events;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
class Event {
	static $fields = array('' => '');
	
	function __construct($form_data = array()){
		if(!empty($form_data['fields'])){
			foreach($form_data['fields'] as $k => $field){
				if(!empty($field['type'])){
					if($field['type'] != 'multi'){
						$label = !empty($field['label']['text']) ? $field['label']['text'] : (!empty($field['label']) ? $field['label'] : (isset($field['value']) ? $field['value'] : ''));
						if(!empty($field['name'])){
							self::$fields[$k] = $field['name'];
						}
						if(!empty($label)){
							if(is_array($label)){
								$label = $label['text'];
							}
							self::$fields[$k] .= ' - '.(strlen($label) < 60 ? $label : substr($label, 0, 60).'...');
						}
					}else{
						foreach($field['inputs'] as $in => $input){
							$label = !empty($input['label']['text']) ? $input['label']['text'] : (!empty($input['label']) ? $input['label'] : (isset($input['value']) ? $input['value'] : null));
							if(!empty($input['name'])){
								self::$fields[$k.'-'.$in] = $input['name'];
							}
							if(!empty($label)){
								self::$fields[$k.'-'.$in] .= ' - '.(strlen($label) < 60 ? $label : substr($label, 0, 60).'...');
							}
						}
					}
				}
			}
		}
	}
	
	public static function _fields($data = array(), $k = '_XNX_'){		
		return array(
			array('name' => 'Form[extras][jsevents]['.$k.'][event]', 'type' => 'dropdown', 'class' => 'events_event_selection', 'label' => array('position' => 'top', 'text' => 'On'), 'sublabel' => 'Event', 'options' => array(
				'' => '',
				'change' => 'Change value',
				'change_to' => 'New value =',
				'change_not' => 'New value !=',
				'check' => 'Check',
				'uncheck' => 'Uncheck',
				'keyup' => 'Typing',
				'click' => 'Click',
			)),
			array('name' => 'Form[extras][jsevents]['.$k.'][value]', 'type' => 'text', 'class' => 'events_value', 'label' => array('position' => 'top', 'text' => 'Value')),
			array('name' => 'Form[extras][jsevents]['.$k.'][source]', 'type' => 'dropdown', 'label' => array('position' => 'top', 'text' => 'Of'), 'sublabel' => 'Element', 'options' => self::$fields),
		);
	}
	
}
?>